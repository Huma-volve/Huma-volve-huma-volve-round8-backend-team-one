<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that anyone can submit a contact message successfully.
     */
    public function test_can_submit_contact_message(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message for the contact form. It should be long enough.',
        ];

        $response = $this->postJson('/api/contact-us', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Your message has been sent successfully. We will get back to you soon.',
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
            ]);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Test that name is required.
     */
    public function test_name_is_required(): void
    {
        $data = [
            'email' => 'john@example.com',
            'message' => 'This is a test message for the contact form.',
        ];

        $response = $this->postJson('/api/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test that email is required.
     */
    public function test_email_is_required(): void
    {
        $data = [
            'name' => 'John Doe',
            'message' => 'This is a test message for the contact form.',
        ];

        $response = $this->postJson('/api/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that email must be valid.
     */
    public function test_email_must_be_valid(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'message' => 'This is a test message for the contact form.',
        ];

        $response = $this->postJson('/api/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that message is required.
     */
    public function test_message_is_required(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $response = $this->postJson('/api/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    /**
     * Test that message must be at least 10 characters.
     */
    public function test_message_must_be_at_least_10_characters(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Short',
        ];

        $response = $this->postJson('/api/contact-us', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    /**
     * Test that contact message is stored with is_read as false by default.
     */
    public function test_contact_message_is_stored_as_unread_by_default(): void
    {
        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'Hello, I would like to know more about your services.',
        ];

        $this->postJson('/api/contact-us', $data);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'is_read' => false,
        ]);
    }
}
