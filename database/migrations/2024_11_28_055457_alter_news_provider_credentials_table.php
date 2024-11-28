<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('news_provider_credentials', function (Blueprint $table) {
			$table->longText('api_key')->nullable()->change();
			$table->longText('secret_key')->nullable()->change();
		});
	}

	public function down(): void
	{
		Schema::table('news_provider_credentials', function (Blueprint $table) {
			$table->string('api_key');
			$table->string('secret_key')
				->nullable();
		});
	}
};
