<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixCustomerNameEncoding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Sửa encoding cho tên khách hàng bị lỗi
        $corrections = [
            'Tr???n' => 'Trần',
            'Ng???c' => 'Ngọc',
            'Tu??n' => 'Tuấn',
            'Ho??ng' => 'Hoàng',
            'Th??i' => 'Thái',
            'V??' => 'Vũ',
            'L??' => 'Lê',
            'Ph???m' => 'Phạm',
            'H???ng' => 'Hương',
            'Th???ng' => 'Thương',
            'Qu???nh' => 'Quỳnh',
            'H??ng' => 'Hưng',
            'Minh' => 'Minh',
            'Th???' => 'Thư',
            'Ch???' => 'Chư',
            'Nh??' => 'Nhà',
            'B???' => 'Bùi',
            '???' => 'ư',
            '??' => 'ã',
            'Qu??' => 'Quý',
            'Chuy??' => 'Chuyên',
            'Ph??' => 'Phú',
            'ng H??' => 'ng Hà',
            'Nguy??' => 'Nguyên',
            'tailieulp' => 'Tài Liệu LP'
        ];

        foreach ($corrections as $wrong => $correct) {
            DB::table('customers')
                ->where('name', 'LIKE', "%{$wrong}%")
                ->update([
                    'name' => DB::raw("REPLACE(name, '{$wrong}', '{$correct}')")
                ]);
        }

        // Log số lượng đã sửa
        $totalCustomers = DB::table('customers')->whereNotNull('name')->count();
        echo "Đã kiểm tra và sửa encoding cho {$totalCustomers} khách hàng.\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Cannot reverse this operation
    }
}
