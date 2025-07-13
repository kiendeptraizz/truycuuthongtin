<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\SupplierProduct;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliersData = [
            [
                'supplier_name' => 'Nguyễn Văn An',
                'products' => [
                    ['product_name' => 'Laptop Dell Inspiron 15', 'price' => 15000000, 'warranty_days' => 365],
                    ['product_name' => 'Laptop HP Pavilion', 'price' => 18000000, 'warranty_days' => 365],
                ]
            ],
            [
                'supplier_name' => 'Công ty TNHH Phong Vũ',
                'products' => [
                    ['product_name' => 'Máy in Canon LBP2900', 'price' => 2500000, 'warranty_days' => 180],
                    ['product_name' => 'Máy in Brother HL-L2321D', 'price' => 3200000, 'warranty_days' => 180],
                ]
            ],
            [
                'supplier_name' => 'Trần Thị Bình',
                'products' => [
                    ['product_name' => 'Văn phòng phẩm tổng hợp', 'price' => 500000, 'warranty_days' => 0],
                    ['product_name' => 'Giấy A4 Double A', 'price' => 120000, 'warranty_days' => 0],
                ]
            ],
            [
                'supplier_name' => 'Công ty Cổ phần FPT',
                'products' => [
                    ['product_name' => 'Phần mềm quản lý', 'price' => 25000000, 'warranty_days' => 1095],
                    ['product_name' => 'Phần mềm kế toán', 'price' => 15000000, 'warranty_days' => 730],
                ]
            ],
            [
                'supplier_name' => 'Lê Hoàng Dũng',
                'products' => [
                    ['product_name' => 'Thiết bị mạng Router', 'price' => 3200000, 'warranty_days' => 730],
                    ['product_name' => 'Switch TP-Link 24 port', 'price' => 2800000, 'warranty_days' => 365],
                ]
            ],
            [
                'supplier_name' => 'Công ty TNHH Thế Giới Di Động',
                'products' => [
                    ['product_name' => 'iPhone 15 Pro Max', 'price' => 32000000, 'warranty_days' => 365],
                    ['product_name' => 'Samsung Galaxy S24', 'price' => 28000000, 'warranty_days' => 365],
                ]
            ],
            [
                'supplier_name' => 'Phan Văn Cường',
                'products' => [
                    ['product_name' => 'Bàn ghế văn phòng', 'price' => 8500000, 'warranty_days' => 730],
                    ['product_name' => 'Tủ tài liệu gỗ', 'price' => 6500000, 'warranty_days' => 365],
                ]
            ],
            [
                'supplier_name' => 'Công ty CP Điện máy Xanh',
                'products' => [
                    ['product_name' => 'Máy lạnh Daikin 1.5HP', 'price' => 12000000, 'warranty_days' => 1095],
                    ['product_name' => 'Tủ lạnh LG 315L', 'price' => 8500000, 'warranty_days' => 730],
                ]
            ],
        ];

        foreach ($suppliersData as $supplierData) {
            // Tạo supplier
            $supplier = Supplier::create([
                'supplier_name' => $supplierData['supplier_name']
            ]);

            // Tạo products cho supplier
            foreach ($supplierData['products'] as $productData) {
                SupplierProduct::create([
                    'supplier_id' => $supplier->id,
                    'product_name' => $productData['product_name'],
                    'price' => $productData['price'],
                    'warranty_days' => $productData['warranty_days'] ?? 0,
                ]);
            }
        }
    }
}
