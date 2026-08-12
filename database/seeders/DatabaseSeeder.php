<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\AgentTask;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء حساب مدير ثابت للتجربة
        $admin = User::create([
            'name' => 'الإدارة العامة',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'), // كلمة المرور الموحدة: password
            'role' => 'admin',
            'phone' => '000000000',
        ]);

        // 2. إنشاء حساب جمعية ثابت
        $org = User::create([
            'name' => 'جمعية الأمل الخيرية',
            'email' => 'org@org.com',
            'password' => bcrypt('password'),
            'role' => 'organization',
            'phone' => '111111111',
        ]);

        // 3. إنشاء حساب متبرع ثابت مع رصيد 5000
        $donor = User::create([
            'name' => 'أحمد المتبرع',
            'email' => 'donor@donor.com',
            'password' => bcrypt('password'),
            'role' => 'donor',
            'phone' => '222222222',
            'wallet_balance' => 5000,
        ]);

        // 4. إنشاء حساب مندوب ثابت
        $agent = User::create([
            'name' => 'خالد المندوب',
            'email' => 'agent@agent.com',
            'password' => bcrypt('password'),
            'role' => 'agent',
            'phone' => '333333333',
        ]);

        // 5. إنشاء بعض المشاريع (الحالات)
       // 5. إنشاء بعض المشاريع (الحالات)
        Project::create([
            'organization_id' => $org->id,
            'title' => 'حفر بئر مياه',
            'description' => 'توفير مياه شرب نقية لقرية نائية تحتاج المساعدة العاجلة.',
            'target_amount' => 1000, // تم التعديل لتطابق المودل
            'collected_amount' => 200,
            'is_urgent' => true,
            'status' => 'approved',
        ]);

        Project::create([
            'organization_id' => $org->id,
            'title' => 'كفالة أيتام',
            'description' => 'كفالة 5 أيتام لمدة سنة كاملة وتوفير مستلزماتهم.',
            'target_amount' => 5000, // تم التعديل لتطابق المودل
            'collected_amount' => 0,
            'is_urgent' => false,
            'status' => 'pending',
        ]);

        // 6. إنشاء مهمة وهمية للمندوب
        AgentTask::create([
            'agent_id' => $agent->id,
            'title' => 'استلام تبرع عيني (ملابس شتوية)',
            'description' => 'التوجه لعنوان المتبرع لاستلام الملابس وتوثيق العملية.',
            'qr_code' => Str::random(10), // كود QR عشوائي
            'status' => 'pending',
        ]);

        $this->command->info('تم زراعة البيانات الوهمية بنجاح! جميع الحسابات كلمة مرورها: password');
    }
}
