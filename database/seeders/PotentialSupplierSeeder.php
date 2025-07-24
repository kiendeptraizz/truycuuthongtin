<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PotentialSupplier;

class PotentialSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $potentialSuppliers = [
            [
                'supplier_name' => 'TechViet Solutions',
                'contact_person' => 'Nguyễn Văn A',
                'phone' => '0901234567',
                'email' => 'contact@techviet.com',
                'address' => '123 Đường ABC, Quận 1, TP.HCM',
                'website' => 'https://techviet.com',
                'notes' => 'Công ty có uy tín trong lĩnh vực cung cấp tài khoản AI',
                'reason_potential' => 'Giá cả cạnh tranh, dịch vụ hỗ trợ 24/7, đánh giá cao từ khách hàng',
                'priority' => 'high',
                'expected_cooperation_date' => '2025-08-01',
                'services' => [
                    [
                        'service_name' => 'ChatGPT Plus',
                        'estimated_price' => 180000,
                        'description' => 'Tài khoản ChatGPT Plus 1 tháng',
                        'unit' => 'tháng',
                        'warranty_days' => 30,
                        'notes' => 'Bảo hành đổi mới nếu lỗi'
                    ],
                    [
                        'service_name' => 'Claude Pro',
                        'estimated_price' => 200000,
                        'description' => 'Tài khoản Claude Pro 1 tháng',
                        'unit' => 'tháng',
                        'warranty_days' => 30,
                        'notes' => 'Hỗ trợ kỹ thuật 24/7'
                    ]
                ]
            ],
            [
                'supplier_name' => 'AI Services Pro',
                'contact_person' => 'Trần Thị B',
                'phone' => '0987654321',
                'email' => 'info@aiservicespro.com',
                'address' => '456 Đường XYZ, Quận 3, TP.HCM',
                'website' => 'https://aiservicespro.com',
                'notes' => 'Chuyên cung cấp các dịch vụ AI cao cấp',
                'reason_potential' => 'Có nhiều dịch vụ độc quyền, giá tốt cho số lượng lớn',
                'priority' => 'medium',
                'expected_cooperation_date' => '2025-09-15',
                'services' => [
                    [
                        'service_name' => 'Midjourney Pro',
                        'estimated_price' => 250000,
                        'description' => 'Tài khoản Midjourney Pro 1 tháng',
                        'unit' => 'tháng',
                        'warranty_days' => 30,
                        'notes' => 'Unlimited generations'
                    ],
                    [
                        'service_name' => 'Gemini Advanced',
                        'estimated_price' => 190000,
                        'description' => 'Tài khoản Google Gemini Advanced',
                        'unit' => 'tháng',
                        'warranty_days' => 30,
                        'notes' => 'Tích hợp với Google Workspace'
                    ]
                ]
            ],
            [
                'supplier_name' => 'Digital Tools Hub',
                'contact_person' => 'Lê Văn C',
                'phone' => '0912345678',
                'email' => 'sales@digitaltoolshub.com',
                'address' => '789 Đường DEF, Quận 7, TP.HCM',
                'website' => null,
                'notes' => 'Nhà cung cấp mới nhưng có tiềm năng',
                'reason_potential' => 'Giá rất cạnh tranh, sẵn sàng hợp tác dài hạn',
                'priority' => 'low',
                'expected_cooperation_date' => '2025-10-01',
                'services' => [
                    [
                        'service_name' => 'Canva Pro',
                        'estimated_price' => 120000,
                        'description' => 'Tài khoản Canva Pro 1 tháng',
                        'unit' => 'tháng',
                        'warranty_days' => 15,
                        'notes' => 'Phù hợp cho thiết kế'
                    ]
                ]
            ]
        ];

        foreach ($potentialSuppliers as $supplierData) {
            $services = $supplierData['services'];
            unset($supplierData['services']);

            $supplier = PotentialSupplier::create($supplierData);

            foreach ($services as $serviceData) {
                $supplier->services()->create($serviceData);
            }
        }
    }
}
