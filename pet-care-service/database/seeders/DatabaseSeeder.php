<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Pet;
use App\Models\Service;
use App\Models\StaffService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Services
        $services = [
            [
                'name' => 'Tắm',
                'description' => 'Dịch vụ tắm chuyên nghiệp cho thú cưng',
                'category' => 'Grooming',
                'price' => 150000,
                'duration_minutes' => 60,
                'is_active' => true
            ],
            [
                'name' => 'Cắt Tỉa Lông',
                'description' => 'Cắt lông định hình theo ý muốn',
                'category' => 'Grooming',
                'price' => 200000,
                'duration_minutes' => 90,
                'is_active' => true
            ],
            [
                'name' => 'Tiêm Phòng',
                'description' => 'Tiêm phòng và chăm sóc sức khỏe',
                'category' => 'Healthcare',
                'price' => 300000,
                'duration_minutes' => 30,
                'is_active' => true
            ],
            [
                'name' => 'Huấn Luyện',
                'description' => 'Huấn luyện thú cưng chuyên nghiệp',
                'category' => 'Training',
                'price' => 250000,
                'duration_minutes' => 120,
                'is_active' => true
            ],
            [
                'name' => 'Chăm Sóc Hộ',
                'description' => 'Dịch vụ chăm sóc thú cưng tại nhà',
                'category' => 'Home Care',
                'price' => 100000,
                'duration_minutes' => 45,
                'is_active' => true
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // Create Admin User (Demo only)
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '0901234567',
            'role' => 'admin',
            'is_active' => true
        ]);

        // Create Customer User (Demo only)
        $demoCustomer = User::create([
            'name' => 'Demo Customer',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
            'phone' => '0912345678',
            'role' => 'customer',
            'is_active' => true
        ]);

        Customer::create([
            'user_id' => $demoCustomer->id,
            'gender' => 'other',
            'address' => 'Hà Nội',
            'city' => 'Hà Nội',
            'postal_code' => '100000',
            'total_spent' => 0
        ]);

        // Create Staff Users (Demo only)
        $staffUsers = [
            [
                'name' => 'Staff User',
                'email' => 'staff@example.com',
                'phone' => '0945678901',
                'code' => 'STAFF001'
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'staff1@example.com',
                'phone' => '0956789012',
                'code' => 'STAFF002'
            ]
        ];

        foreach ($staffUsers as $staffData) {
            $user = User::create([
                'name' => $staffData['name'],
                'email' => $staffData['email'],
                'password' => Hash::make('password'),
                'phone' => $staffData['phone'],
                'role' => 'staff',
                'is_active' => true
            ]);

            $staff = Staff::create([
                'user_id' => $user->id,
                'staff_code' => $staffData['code'],
                'position' => 'Pet Care Specialist',
                'specialization' => 'Grooming, Training',
                'hire_date' => now(),
                'rating' => 4.5,
                'total_appointments' => 0
            ]);

            // Assign services to staff
            $serviceIds = Service::pluck('id')->random(rand(2, 4))->toArray();
            foreach ($serviceIds as $serviceId) {
                StaffService::create([
                    'staff_id' => $staff->id,
                    'service_id' => $serviceId
                ]);
            }
        }
    }
}
