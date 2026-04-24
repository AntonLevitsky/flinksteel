<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('po_number')->nullable()->after('order_number');

            $table->string('billing_company_name')->nullable()->after('total_eur');
            $table->string('billing_street')->nullable()->after('billing_company_name');
            $table->string('billing_postal_code', 10)->nullable()->after('billing_street');
            $table->string('billing_city')->nullable()->after('billing_postal_code');
            $table->string('billing_vat_id')->nullable()->after('billing_city');
            $table->string('billing_email')->nullable()->after('billing_vat_id');

            $table->string('delivery_company_name')->nullable()->after('delivery_city');
            $table->string('delivery_contact_name')->nullable()->after('delivery_company_name');
            $table->string('delivery_contact_phone')->nullable()->after('delivery_contact_name');
            $table->string('delivery_window')->nullable()->after('delivery_contact_phone');

            $table->integer('payment_terms_days')->default(30)->after('delivery_window');
            $table->date('payment_due_date')->nullable()->after('payment_terms_days');
            $table->string('shipping_option_code')->nullable()->after('payment_due_date');
            $table->string('shipping_option_label')->nullable()->after('shipping_option_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'po_number',
                'billing_company_name',
                'billing_street',
                'billing_postal_code',
                'billing_city',
                'billing_vat_id',
                'billing_email',
                'delivery_company_name',
                'delivery_contact_name',
                'delivery_contact_phone',
                'delivery_window',
                'payment_terms_days',
                'payment_due_date',
                'shipping_option_code',
                'shipping_option_label',
            ]);
        });
    }
};
