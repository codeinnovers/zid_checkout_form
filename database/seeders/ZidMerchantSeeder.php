<?php

namespace Database\Seeders;

use App\Enums\FormFieldStatusEnum;
use App\Enums\FormSubmissionStatusEnum;
use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use App\Models\User;
use App\Models\ZidFormField;
use App\Models\ZidFormSubmission;
use App\Models\ZidMerchant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ZidMerchantSeeder extends Seeder
{
    public function run(): void
    {
        // ── Fixed merchant with known credentials ──────────────────────────
        $merchantUser = User::updateOrCreate(
            ['email' => 'merchant@example.com'],
            [
                'name'     => 'Sample Merchant',
                'role'     => UserRoleEnum::Merchant,
                'status'   => UserStatusEnum::Active,
                'password' => Hash::make('password'),
            ]
        );

        $merchant = ZidMerchant::updateOrCreate(
            ['user_id' => $merchantUser->id],
            [
                'name'             => 'Al-Riyadh Fashion Store',
                'username'         => 'alriyadh_fashion',
                'phone'            => '+966512345678',
                'email'            => 'store@alriyadh-fashion.com',
                'token'            => hash('sha256', 'token-' . $merchantUser->id),
                'xmanager_token'   => hash('sha256', 'xmanager-' . $merchantUser->id),
                'refresh_token'    => hash('sha256', 'refresh-' . $merchantUser->id),
                'token_expiration' => now()->addYear(),
                'store_reference'  => 'store-001-riyadh',
            ]
        );

        // ── Form fields ────────────────────────────────────────────────────
        $fieldDefinitions = [
            [
                'field_type'  => 'text',
                'name'        => 'customer_name',
                'label'       => 'Customer Name',
                'label_ar'    => 'اسم العميل',
                'placeholder' => 'Enter your full name',
                'placeholder_ar' => 'أدخل اسمك الكامل',
                'is_required' => true,
                'sort_order'  => 1,
            ],
            [
                'field_type'  => 'phone',
                'name'        => 'customer_phone',
                'label'       => 'Phone Number',
                'label_ar'    => 'رقم الهاتف',
                'placeholder' => '05xxxxxxxx',
                'placeholder_ar' => '05xxxxxxxx',
                'is_required' => true,
                'sort_order'  => 2,
            ],
            [
                'field_type'  => 'select',
                'name'        => 'gift_wrapping',
                'label'       => 'Gift Wrapping',
                'label_ar'    => 'تغليف الهدايا',
                'options'     => [
                    ['value' => 'none',     'label' => 'No Wrapping'],
                    ['value' => 'standard', 'label' => 'Standard'],
                    ['value' => 'premium',  'label' => 'Premium (+5 SAR)'],
                ],
                'is_required' => false,
                'sort_order'  => 3,
            ],
            [
                'field_type'  => 'textarea',
                'name'        => 'special_instructions',
                'label'       => 'Special Instructions',
                'label_ar'    => 'تعليمات خاصة',
                'placeholder' => 'Any special requests for your order...',
                'placeholder_ar' => 'أي طلبات خاصة لطلبك...',
                'is_required' => false,
                'sort_order'  => 4,
            ],
        ];

        foreach ($fieldDefinitions as $fieldData) {
            ZidFormField::updateOrCreate(
                ['zid_merchant_id' => $merchant->id, 'name' => $fieldData['name']],
                array_merge($fieldData, [
                    'zid_merchant_id' => $merchant->id,
                    'status'          => FormFieldStatusEnum::Active,
                ])
            );
        }

        // ── Sample submissions ─────────────────────────────────────────────
        $submissions = [
            ['name' => 'Ahmed Al-Ghamdi',    'phone' => '+966501234567', 'gift' => 'standard', 'note' => 'Please deliver in the morning',       'status' => FormSubmissionStatusEnum::Processed, 'days' => 10],
            ['name' => 'Fatima Al-Zahrani',  'phone' => '+966509876543', 'gift' => 'premium',  'note' => null,                                   'status' => FormSubmissionStatusEnum::Processed, 'days' => 8],
            ['name' => 'Mohammed Al-Harbi',  'phone' => '+966512345678', 'gift' => 'none',     'note' => 'Leave at the door',                    'status' => FormSubmissionStatusEnum::Pending,   'days' => 6],
            ['name' => 'Sara Al-Otaibi',     'phone' => '+966555566677', 'gift' => 'standard', 'note' => null,                                   'status' => FormSubmissionStatusEnum::Pending,   'days' => 4],
            ['name' => 'Khalid Al-Shammari', 'phone' => '+966544433322', 'gift' => 'none',     'note' => 'Call before delivery',                 'status' => FormSubmissionStatusEnum::Pending,   'days' => 3],
            ['name' => 'Noura Al-Dosari',    'phone' => '+966533221100', 'gift' => 'premium',  'note' => 'Fragile items inside, handle carefully','status' => FormSubmissionStatusEnum::Pending,   'days' => 2],
            ['name' => 'Omar Al-Mutairi',    'phone' => '+966566778899', 'gift' => 'standard', 'note' => null,                                   'status' => FormSubmissionStatusEnum::Pending,   'days' => 1],
        ];

        foreach ($submissions as $i => $s) {
            $formData = array_filter([
                'customer_name'        => $s['name'],
                'customer_phone'       => $s['phone'],
                'gift_wrapping'        => $s['gift'],
                'special_instructions' => $s['note'],
            ], fn ($v) => $v !== null);

            ZidFormSubmission::firstOrCreate(
                ['zid_merchant_id' => $merchant->id, 'order_number' => 'ORD-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT)],
                [
                    'status'     => $s['status'],
                    'form_data'  => $formData,
                    'ip_address' => '192.168.1.' . ($i + 1),
                    'created_at' => now()->subDays($s['days']),
                    'updated_at' => now()->subDays($s['days']),
                ]
            );
        }

        // ── Random merchants for admin panel testing ───────────────────────
        ZidMerchant::factory()
            ->count(5)
            ->create();
    }
}
