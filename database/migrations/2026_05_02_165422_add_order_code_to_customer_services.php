<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mọi CustomerService phải có order_code (DH-YYMMDD-XXX) để tra cứu —
 * kể cả khi tạo từ web không qua bot.
 *
 * - Schema: cột nullable + unique (cho phép NULL trong khi backfill).
 * - Backfill: copy từ pending_orders nếu có pending_order_id, không thì
 *   gen mới theo created_at + sequence trong ngày.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_services', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_services', 'order_code')) {
                $table->string('order_code', 32)->nullable()->unique()->after('id');
            }
        });

        // Backfill data hiện tại
        $this->backfillOrderCodes();
    }

    public function down(): void
    {
        Schema::table('customer_services', function (Blueprint $table) {
            if (Schema::hasColumn('customer_services', 'order_code')) {
                $table->dropUnique(['order_code']);
                $table->dropColumn('order_code');
            }
        });
    }

    /**
     * Backfill order_code cho records hiện tại.
     */
    private function backfillOrderCodes(): void
    {
        // 1) Copy từ pending_orders nếu có link
        DB::statement("
            UPDATE customer_services cs
            INNER JOIN pending_orders po ON po.id = cs.pending_order_id
            SET cs.order_code = po.order_code
            WHERE cs.order_code IS NULL AND po.order_code IS NOT NULL
        ");

        // 2) Gen mới cho records còn lại — theo created_at + sequence trong ngày
        $rows = DB::table('customer_services')
            ->whereNull('order_code')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'created_at']);

        if ($rows->isEmpty()) return;

        $seqByDate = []; // 'YYMMDD' => last_seq
        foreach ($rows as $row) {
            $date = $row->created_at ? new \DateTime($row->created_at) : new \DateTime();
            $datePart = $date->format('ymd');

            // Tìm seq lớn nhất trong ngày (đã có trong cs hoặc pending_orders) để tránh trùng
            if (!isset($seqByDate[$datePart])) {
                $maxSeq = 0;

                $maxFromCs = DB::table('customer_services')
                    ->where('order_code', 'like', "DH-{$datePart}-%")
                    ->orderByDesc('order_code')
                    ->value('order_code');
                if ($maxFromCs && preg_match('/-(\d+)$/', $maxFromCs, $m)) {
                    $maxSeq = max($maxSeq, (int) $m[1]);
                }

                $maxFromPo = DB::table('pending_orders')
                    ->where('order_code', 'like', "DH-{$datePart}-%")
                    ->orderByDesc('order_code')
                    ->value('order_code');
                if ($maxFromPo && preg_match('/-(\d+)$/', $maxFromPo, $m)) {
                    $maxSeq = max($maxSeq, (int) $m[1]);
                }

                $seqByDate[$datePart] = $maxSeq;
            }

            $seqByDate[$datePart]++;
            $code = sprintf('DH-%s-%03d', $datePart, $seqByDate[$datePart]);

            DB::table('customer_services')
                ->where('id', $row->id)
                ->update(['order_code' => $code]);
        }
    }
};
