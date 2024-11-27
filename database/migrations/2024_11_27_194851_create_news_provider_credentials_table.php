<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('news_provider_credentials', function (Blueprint $table) {
			$table->id();
			$table->foreignId('news_provider_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
			$table->string('api_key');
			$table->string('secret_key')
				->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('news_provider_credentials');
	}
};
