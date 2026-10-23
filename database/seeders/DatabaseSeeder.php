<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Roles
        $adminRole = Role::create([
            'name' => 'admin',
            'name_ar' => 'مدير النظام',
            'description' => 'صلاحيات إدارية كاملة لإدارة النظام والتذاكر والمستخدمين',
            'permissions' => ['all'],
        ]);

        $supportRole = Role::create([
            'name' => 'support',
            'name_ar' => 'فريق الدعم الفني',
            'description' => 'استقبال التذاكر وتحديث حالتها وإضافة ملاحظات داخلية وحل المشكلات',
            'permissions' => ['view_tickets', 'update_tickets', 'reply_tickets', 'internal_notes'],
        ]);

        $userRole = Role::create([
            'name' => 'user',
            'name_ar' => 'مستخدم',
            'description' => 'فتح ومتابعة تذاكر الدعم الفني الخاصة به',
            'permissions' => ['create_tickets', 'view_own_tickets', 'reply_own_tickets'],
        ]);

        // 2. Create Departments
        $itDept = Department::create([
            'name' => 'IT Support',
            'name_ar' => 'الدعم الفني والتقني',
            'description' => 'قسم حل مشاكل الأجهزة والبرمجيات والمساعدة التقنية',
            'is_active' => true,
        ]);

        $netDept = Department::create([
            'name' => 'Networks & Infrastructure',
            'name_ar' => 'الشبكات والبنية التحتية',
            'description' => 'قسم صيانة الشبكات، السيرفرات والاتصالات',
            'is_active' => true,
        ]);

        $devDept = Department::create([
            'name' => 'Software & Systems',
            'name_ar' => 'الأنظمة والبرمجيات',
            'description' => 'قسم تطوير الأنظمة وقواعد البيانات وتطبيقات العمل',
            'is_active' => true,
        ]);

        // 3. Create Users
        $admin = User::create([
            'name' => 'سعد الغامدي',
            'email' => 'admin@support.local',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'department_id' => $itDept->id,
            'phone' => '0555123456',
            'employee_id' => 'EMP-001',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $support1 = User::create([
            'name' => 'فهد المطيري',
            'email' => 'support@support.local',
            'password' => Hash::make('password'),
            'role_id' => $supportRole->id,
            'department_id' => $itDept->id,
            'phone' => '0555654321',
            'employee_id' => 'EMP-002',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $support2 = User::create([
            'name' => 'نورة القحطاني',
            'email' => 'support2@support.local',
            'password' => Hash::make('password'),
            'role_id' => $supportRole->id,
            'department_id' => $netDept->id,
            'phone' => '0555987654',
            'employee_id' => 'EMP-003',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $user1 = User::create([
            'name' => 'أحمد الشهري',
            'email' => 'user@support.local',
            'password' => Hash::make('password'),
            'role_id' => $userRole->id,
            'department_id' => $devDept->id,
            'phone' => '0555112233',
            'employee_id' => 'EMP-010',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $user2 = User::create([
            'name' => 'سارة العتيبي',
            'email' => 'user2@support.local',
            'password' => Hash::make('password'),
            'role_id' => $userRole->id,
            'department_id' => $itDept->id,
            'phone' => '0555445566',
            'employee_id' => 'EMP-011',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Set department supervisors
        $itDept->update(['supervisor_id' => $admin->id]);
        $netDept->update(['supervisor_id' => $support2->id]);
        $devDept->update(['supervisor_id' => $admin->id]);

        // 4. Create Equipment
        $eq1 = Equipment::create([
            'serial_number' => 'SN-DELL-88492',
            'type' => 'laptop',
            'brand' => 'Dell',
            'model' => 'Latitude 5520',
            'specifications' => 'Intel Core i7-1185G7, 16GB RAM, 512GB NVMe SSD',
            'assigned_to' => $user1->id,
            'department_id' => $devDept->id,
            'status' => 'in_use',
            'purchase_date' => '2023-05-10',
            'warranty_expiry' => '2026-05-10',
            'notes' => 'جهاز المطور الرئيسي',
        ]);

        $eq2 = Equipment::create([
            'serial_number' => 'SN-THINK-44211',
            'type' => 'laptop',
            'brand' => 'Lenovo',
            'model' => 'ThinkPad X1 Carbon Gen 10',
            'specifications' => 'Intel Core i7-1260P, 32GB RAM, 1TB SSD',
            'assigned_to' => $user2->id,
            'department_id' => $itDept->id,
            'status' => 'in_use',
            'purchase_date' => '2023-08-15',
            'warranty_expiry' => '2026-08-15',
            'notes' => 'جهاز مهندس النظم',
        ]);

        $eq3 = Equipment::create([
            'serial_number' => 'SN-HP-99012',
            'type' => 'printer',
            'brand' => 'HP',
            'model' => 'LaserJet Pro M404dn',
            'specifications' => 'Monochrome Laser Printer, Network Enabled',
            'assigned_to' => null,
            'department_id' => $itDept->id,
            'status' => 'available',
            'purchase_date' => '2022-01-20',
            'warranty_expiry' => '2025-01-20',
            'notes' => 'طابعة مكتبية مشتركة الدور الثاني',
        ]);

        // 5. Create Sample Tickets
        $ticket1 = Ticket::create([
            'ticket_number' => 'TKT-2026-00001',
            'title' => 'انقطاع الاتصال بالشبكة الداخلية وسيرفر الملفات',
            'description' => 'أواجه مشكلة في الوصول إلى مجلد المشاركة وسيرفر الملفات المحلي منذ الصباح، تظهر رسالة خطأ بالاتصال مع أن الإنترنت الخارجي يعمل بشكل طبيعي.',
            'priority' => 'urgent',
            'status' => 'in_progress',
            'category' => 'network',
            'created_by' => $user1->id,
            'assigned_to' => $support2->id,
            'department_id' => $netDept->id,
            'equipment_id' => $eq1->id,
            'assigned_at' => now()->subHours(4),
            'created_at' => now()->subHours(5),
        ]);

        TicketReply::create([
            'ticket_id' => $ticket1->id,
            'user_id' => $support2->id,
            'message' => 'أهلاً بك أخي أحمد، جاري التحقق من إعدادات الـ VLAN والـ DNS لسيرفر الملفات، يرجى إعادة تشغيل كارت الشبكة وموافاتنا بالنتيجة.',
            'is_internal' => false,
            'is_system' => false,
            'created_at' => now()->subHours(3),
        ]);

        TicketReply::create([
            'ticket_id' => $ticket1->id,
            'user_id' => $support2->id,
            'message' => 'ملاحظة داخلية: تم فحص السويتش الرئيسي، توجد إعادة ضبط جارية لخدمة Samba.',
            'is_internal' => true,
            'is_system' => false,
            'created_at' => now()->subHours(2),
        ]);

        $ticket2 = Ticket::create([
            'ticket_number' => 'TKT-2026-00002',
            'title' => 'عطل الشاشة الزرقاء (BSOD) عند تشغيل برنامج المحاكاة',
            'description' => 'عند تشغيل بيئة Docker وتطبيق المحاكاة يتوقف الجهاز فجأة وتظهر شاشة الموت الزرقاء مع كود DRIVER_IRQL_NOT_LESS_OR_EQUAL.',
            'priority' => 'high',
            'status' => 'assigned',
            'category' => 'hardware',
            'created_by' => $user2->id,
            'assigned_to' => $support1->id,
            'department_id' => $itDept->id,
            'equipment_id' => $eq2->id,
            'assigned_at' => now()->subHours(2),
            'created_at' => now()->subHours(3),
        ]);

        TicketReply::create([
            'ticket_id' => $ticket2->id,
            'user_id' => $support1->id,
            'message' => 'تم استلام التذكرة وجارٍ فحص سجلات النظام وتحديث تعريف كارت الشاشة والذاكرة العشوائية.',
            'is_internal' => false,
            'is_system' => false,
            'created_at' => now()->subHours(1),
        ]);

        $ticket3 = Ticket::create([
            'ticket_number' => 'TKT-2026-00003',
            'title' => 'طلب ترقية سعة الذاكرة العشوائية RAM للجهاز المحمول',
            'description' => 'نظراً لحجم المشاريع البرمجية الحالية نحتاج إلى ترقية الذاكرة العشوائية من 16 جيجابايت إلى 32 جيجابايت لضمان استقرار بيئة التطوير.',
            'priority' => 'medium',
            'status' => 'open',
            'category' => 'hardware',
            'created_by' => $user1->id,
            'assigned_to' => null,
            'department_id' => $itDept->id,
            'equipment_id' => $eq1->id,
            'created_at' => now()->subHours(1),
        ]);

        $ticket4 = Ticket::create([
            'ticket_number' => 'TKT-2026-00004',
            'title' => 'تم حل المشكلة: إعادة تهيئة تعريف طابعة الدور الثاني',
            'description' => 'الطابعة لم تكن تستقبل أوامر الطباعة المرسلة عبر البروتوكول اللاسلكي.',
            'priority' => 'low',
            'status' => 'resolved',
            'category' => 'hardware',
            'created_by' => $user2->id,
            'assigned_to' => $support1->id,
            'department_id' => $itDept->id,
            'equipment_id' => $eq3->id,
            'assigned_at' => now()->subDays(1),
            'resolved_at' => now()->subHours(6),
            'created_at' => now()->subDays(1),
        ]);

        TicketReply::create([
            'ticket_id' => $ticket4->id,
            'user_id' => $support1->id,
            'message' => 'تم تحديث الـ Firmware وإعادة ضبط عنوان IP الثابت للطابعة والتحقق من عملها بنجاح.',
            'is_internal' => false,
            'is_system' => false,
            'created_at' => now()->subHours(6),
        ]);
    }
}
