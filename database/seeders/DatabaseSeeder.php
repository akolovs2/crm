<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'is_admin' => true,
        ]);

        $tags = Tag::factory(8)->create();

        $companies = Company::factory(20)->create(['owner_id' => $user->id]);

        $contacts = Contact::factory(50)->create([
            'owner_id' => $user->id,
            'company_id' => fn () => $companies->random()->id,
        ]);

        $deals = Deal::factory(30)->create([
            'owner_id' => $user->id,
            'contact_id' => fn () => $contacts->random()->id,
            'company_id' => fn () => $companies->random()->id,
        ]);

        // Attach random tags to contacts, companies, deals
        $contacts->each(fn ($c) => $c->tags()->attach($tags->random(rand(0, 3))));
        $companies->each(fn ($c) => $c->tags()->attach($tags->random(rand(0, 2))));
        $deals->each(fn ($d) => $d->tags()->attach($tags->random(rand(0, 2))));

        // Seed activities on contacts and deals
        $contacts->random(20)->each(function ($contact) use ($user) {
            Activity::factory(rand(1, 4))->create([
                'owner_id' => $user->id,
                'activityable_type' => Contact::class,
                'activityable_id' => $contact->id,
            ]);
        });

        $deals->random(15)->each(function ($deal) use ($user) {
            Activity::factory(rand(1, 3))->create([
                'owner_id' => $user->id,
                'activityable_type' => Deal::class,
                'activityable_id' => $deal->id,
            ]);
        });
    }
}
