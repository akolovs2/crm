<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // index
    // -------------------------------------------------------------------------

    public function test_guests_cannot_view_companies()
    {
        $this->get(route('companies.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_companies()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('companies.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Companies/Index'));
    }

    public function test_companies_index_returns_paginated_list()
    {
        $user = User::factory()->create();
        Company::factory(5)->create(['owner_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('companies.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Companies/Index')
                ->has('companies.data', 5)
            );
    }

    public function test_companies_index_search_filters_by_name()
    {
        $user = User::factory()->create();
        Company::factory()->create(['name' => 'Acme Corp', 'owner_id' => $user->id]);
        Company::factory()->create(['name' => 'Globex', 'owner_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('companies.index', ['search' => 'Acme']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('companies.data', 1)
                ->where('companies.data.0.name', 'Acme Corp')
            );
    }

    // -------------------------------------------------------------------------
    // create
    // -------------------------------------------------------------------------

    public function test_guests_cannot_view_create_company_form()
    {
        $this->get(route('companies.create'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_create_company_form()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('companies.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Companies/Create'));
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_guests_cannot_create_company()
    {
        $this->post(route('companies.store'), [])->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_company()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('companies.store'), [
                'name'     => 'Acme Corp',
                'industry' => 'Technology',
                'website'  => 'https://acme.example.com',
                'email'    => 'info@acme.example.com',
                'city'     => 'New York',
                'country'  => 'US',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('companies', [
            'name'     => 'Acme Corp',
            'industry' => 'Technology',
            'owner_id' => $user->id,
        ]);
    }

    public function test_company_is_created_with_only_required_fields()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('companies.store'), ['name' => 'Minimal Inc'])
            ->assertRedirect();

        $this->assertDatabaseHas('companies', ['name' => 'Minimal Inc', 'owner_id' => $user->id]);
    }

    public function test_store_requires_name()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('companies.store'), ['industry' => 'Tech'])
            ->assertSessionHasErrors('name');
    }

    public function test_store_rejects_duplicate_name()
    {
        $user = User::factory()->create();
        Company::factory()->create(['name' => 'Taken Corp', 'owner_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('companies.store'), ['name' => 'Taken Corp'])
            ->assertSessionHasErrors('name');
    }

    public function test_store_rejects_invalid_website_url()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('companies.store'), [
                'name'    => 'Bad URL Corp',
                'website' => 'not-a-url',
            ])
            ->assertSessionHasErrors('website');
    }

    public function test_store_rejects_invalid_email()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('companies.store'), [
                'name'  => 'Bad Email Corp',
                'email' => 'not-an-email',
            ])
            ->assertSessionHasErrors('email');
    }

    // -------------------------------------------------------------------------
    // show
    // -------------------------------------------------------------------------

    public function test_guests_cannot_view_company()
    {
        $company = Company::factory()->create();

        $this->get(route('companies.show', $company))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_company()
    {
        $user    = User::factory()->create();
        $company = Company::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('companies.show', $company))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Companies/Show')
                ->where('company.id', $company->id)
            );
    }

    public function test_company_show_includes_contacts()
    {
        $user    = User::factory()->create();
        $company = Company::factory()->create(['owner_id' => $user->id]);
        Contact::factory(3)->create(['company_id' => $company->id, 'owner_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('companies.show', $company))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('company.contacts', 3)
            );
    }

    // -------------------------------------------------------------------------
    // edit
    // -------------------------------------------------------------------------

    public function test_guests_cannot_view_edit_company_form()
    {
        $company = Company::factory()->create();

        $this->get(route('companies.edit', $company))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_edit_company_form()
    {
        $user    = User::factory()->create();
        $company = Company::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('companies.edit', $company))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Companies/Edit'));
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_guests_cannot_update_company()
    {
        $company = Company::factory()->create();

        $this->put(route('companies.update', $company), [])->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_update_company()
    {
        $user    = User::factory()->create();
        $company = Company::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user)
            ->put(route('companies.update', $company), [
                'name'    => 'Updated Corp',
                'city'    => 'Chicago',
                'country' => 'US',
            ])
            ->assertRedirect(route('companies.show', $company));

        $this->assertDatabaseHas('companies', [
            'id'   => $company->id,
            'name' => 'Updated Corp',
            'city' => 'Chicago',
        ]);
    }

    public function test_company_can_keep_its_own_name_on_update()
    {
        $user    = User::factory()->create();
        $company = Company::factory()->create(['name' => 'My Corp', 'owner_id' => $user->id]);

        $this->actingAs($user)
            ->put(route('companies.update', $company), [
                'name' => 'My Corp',
                'city' => 'Boston',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => 'My Corp']);
    }

    public function test_update_rejects_duplicate_name_from_another_company()
    {
        $user = User::factory()->create();
        Company::factory()->create(['name' => 'Taken Corp', 'owner_id' => $user->id]);
        $company = Company::factory()->create(['name' => 'My Corp', 'owner_id' => $user->id]);

        $this->actingAs($user)
            ->put(route('companies.update', $company), ['name' => 'Taken Corp'])
            ->assertSessionHasErrors('name');
    }

    public function test_update_requires_name()
    {
        $user    = User::factory()->create();
        $company = Company::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user)
            ->put(route('companies.update', $company), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    public function test_guests_cannot_delete_company()
    {
        $company = Company::factory()->create();

        $this->delete(route('companies.destroy', $company))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_delete_company()
    {
        $user    = User::factory()->create();
        $company = Company::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('companies.destroy', $company))
            ->assertRedirect(route('companies.index'));

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    public function test_deleting_company_nullifies_contact_company_id()
    {
        $user    = User::factory()->create();
        $company = Company::factory()->create(['owner_id' => $user->id]);
        $contact = Contact::factory()->create(['company_id' => $company->id, 'owner_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('companies.destroy', $company));

        $this->assertDatabaseHas('contacts', ['id' => $contact->id, 'company_id' => null]);
    }
}
