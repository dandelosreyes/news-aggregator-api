<?php

namespace Domain\NewsProviders\Console\Commands;

use Domain\NewsProviders\Models\NewsProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\confirm;

class AddNewsProviderCredentialsCommand extends Command
{
	protected $signature = 'add:news-provider-credentials';

	protected $description = 'Command description';

	public function handle(): void
	{
		$newsProviders = NewsProvider::with(['credentials'])
			->get();

		$newsProvidersArray = $newsProviders->pluck('name', 'id')->toArray();

		$newsProvider = select(
			label: 'Choose the news provider',
			options: $newsProvidersArray
		);

		$confirmed = confirm(
			label: 'Doing this will overwrite the existing credentials. Are you sure you want to continue?',
			default: false,
			yes: "I accept",
			no: "I decline",
		);

		if (! $confirmed) {
			$this->info('Operation cancelled');
			return;
		}

		$newsProvider = $newsProviders->filter(fn ($item) => $item->id === $newsProvider)->first();

		$apiKey = password('Enter the API Key for the news provider');
		$secretKey = password('Enter the Secret Key for the news provider');

		if ($newsProvider->credentials->isNotEmpty()) {
			$newsProvider->credentials()->delete();
		}

		$newsProvider->credentials()->create([
			'api_key' => Crypt::encryptString($apiKey),
			'secret_key' => Crypt::encryptString($secretKey),
		]);

		$this->info('Credentials added successfully!');
	}
}
