<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable'); // ticket_id or ticket_reply_id
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();

            // File information
            $table->string('original_name'); // الاسم الأصلي للملف
            $table->string('stored_name'); // اسم الملف المخزن (hash)
            $table->string('mime_type');
            $table->unsignedBigInteger('size'); // بالبايت
            $table->string('path'); // المسار في storage
            $table->string('disk')->default('private'); // القرص المستخدم

            // Security & metadata
            $table->string('hash')->nullable(); // SHA256 hash للتحقق من سلامة الملف
            $table->boolean('is_scanned')->default(false); // فحص الفيروسات
            $table->boolean('is_safe')->default(false);

            $table->timestamps();

            $table->index('uploaded_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
