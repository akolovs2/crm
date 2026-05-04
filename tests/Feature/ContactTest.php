<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // index
    // -------------------------------------------------------------------------

    public function test_guests_cannot_view_contacts()
    {
        $this->get(route('contacts.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_contacts()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('contacts.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Contacts/Index'));
    }

    public function test_contacts_index_returns_paginated_list()
    {
        $user = User::factory()->create();
        Contact::factory(5)->create(['owner_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('contacts.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Contacts/Index')
                ->has('contacts.data', 5)
            );
    }

    public function test_contacts_index_search_filters_results()
    {
        $user = User::factory()->create();
        Contact::factory()->create(['first_name' => 'Alice', 'last_name' => 'Smith', 'owner_id' => $user->id]);
        Contact::factory()->create(['first_name' => 'Bob', 'last_name' => 'Jones', 'owner_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('contacts.index', ['search' => 'Alice']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('contacts.data', 1)
                ->where('contacts.data.0.first_name', 'Alice')
            );
    }

    // -------------------------------------------------------------------------
    // create
    // -------------------------------------------------------------------------

    public function test_guests_cannot_view_create_contact_form()
    {
        $this->get(route('contacts.create'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_create_contact_form()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('contacts.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Contacts/Create'));
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_guests_cannot_create_contact()
    {
        $this->post(route('contacts.store'), [])->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_contact()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('contacts.store'), [
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'email'      => 'jane@example.com',
                'phone'      => '555-0100',
                'job_title'  => 'Developer',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('contacts', [
            'first_name' => 'Jane',
            'last_name'  => 'Doe',
            'email'      => 'jane@example.com',
            'owner_id'   => $user->id,
        ]);
    }

    public function test_contact_is_created_without_optional_fields()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('contacts.store'), [
                'first_name' => 'John',
                'last_name'  => 'Smith',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('contacts', ['first_name' => 'John', 'owner_id' => $user->id]);
    }

    public function test_contact_can_be_linked_to_company_on_create()
    {
        $user    = User::factory()->create();
        $company = Company::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('contacts.store'), [
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'company_id' => $company->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('contacts', ['last_name' => 'Doe', 'company_id' => $company->id]);
    }

    public function test_store_requires_first_name()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('contacts.store'), ['last_name' => 'Doe'])
            ->assertSessionHasErrors('first_name');
    }

    public function test_store_requires_last_name()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('contacts.store'), ['first_name' => 'Jane'])
            ->assertSessionHasErrors('last_name');
    }

    public function test_store_rejects_duplicate_email()
    {
        $user = User::factory()->create();
        Contact::factory()->create(['email' => 'taken@example.com', 'owner_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('contacts.store'), [
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'email'      => 'taken@example.com',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_store_rejects_invalid_email()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('contacts.store'), [
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'email'      => 'not-an-email',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_store_rejects_nonexistent_company()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('contacts.store'), [
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'company_id' => 99999,
            ])
            ->assertSessionHasErrors('company_id');
    }

    // -------------------------------------------------------------------------
    // show
    // -------------------------------------------------------------------------

    public function test_guests_cannot_view_contact()
    {
        $contact = Contact::factory()->create();

        $this->get(route('contacts.show', $contact))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_contact()
    {
        $user    = User::factory()->create();
        $contact = Contact::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('contacts.show', $contact))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Contacts/Show')
                ->where('contact.id', $contact->id)
            );
    }

    // -------------------------------------------------------------------------
    // edit
    // -------------------------------------------------------------------------

    public function test_guests_cannot_view_edit_contact_form()
    {
        $contact = Contact::factory()->create();

        $this->get(route('contacts.edit', $contact))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_edit_contact_form()
    {
        $user    = User::factory()->create();
        $contact = Contact::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('contacts.edit', $contact))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Contacts/Edit'));
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_guests_cannot_update_contact()
    {
        $contact = Contact::factory()->create();

        $this->put(route('contacts.update', $contact), [])->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_update_contact()
    {
        $user    = User::factory()->create();
        $contact = Contact::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user)
            ->put(route('contacts.update', $contact), [
                'first_name' => 'Updated',
                'last_name'  => 'Name',
                'email'      => 'updated@example.com',
            ])
            ->assertRedirect(route('contacts.show', $contact));

        $this->assertDatabaseHas('contacts', [
            'id'         => $contact->id,
            'first_name' => 'Updated',
            'email'      => 'updated@example.com',
        ]);
    }

    public function test_contact_can_keep_its_own_email_on_update()
    {
        $user    = User::factory()->create();
        $contact = Contact::factory()->create([
            'email'    => 'mine@example.com',
            'owner_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->put(route('contacts.update', $contact), [
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'email'      => 'mine@example.com',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('contacts', ['id' => $contact->id, 'email' => 'mine@example.com']);
    }

    public function test_update_rejects_duplicate_email_from_another_contact()
    {
        $user    = User::factory()->create();
        Contact::factory()->create(['email' => 'taken@example.com', 'owner_id' => $user->id]);
        $contact = Contact::factory()->create(['email' => 'mine@example.com', 'owner_id' => $user->id]);

        $this->actingAs($user)
            ->put(route('contacts.update', $contact), [
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'email'      => 'taken@example.com',
            ])
            ->assertSessionHasErrors('email');
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    public function test_guests_cannot_delete_contact()
    {
        $contact = Contact::factory()->create();

        $this->delete(route('contacts.destroy', $contact))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_delete_contact()
    {
        $user    = User::factory()->create();
        $contact = Contact::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('contacts.destroy', $contact))
            ->assertRedirect(route('contacts.index'));

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
