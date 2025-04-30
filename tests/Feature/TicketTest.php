<?php

use Herd\SupportTicket\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a sample user for testing
    $this->user = new class extends \Illuminate\Foundation\Auth\User {
        protected $fillable = ['name', 'email', 'password'];
    };
    
    $this->user->forceFill([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    
    // Mock authorization to always return true for simplicity in tests
    $this->mock(\Illuminate\Contracts\Auth\Access\Gate::class, function ($mock) {
        $mock->shouldReceive('check')->andReturn(true);
        $mock->shouldReceive('authorize')->andReturn(true);
        $mock->shouldReceive('allows')->andReturn(true);
    });
});

test('can create a ticket', function () {
    $ticket = Ticket::create([
        'subject' => 'Test Subject',
        'message' => 'Test message content',
        'status' => Ticket::STATUS_OPEN,
        'user_id' => $this->user->id,
        'user_type' => get_class($this->user),
    ]);

    expect($ticket)->toBeInstanceOf(Ticket::class)
        ->and($ticket->subject)->toBe('Test Subject')
        ->and($ticket->status)->toBe(Ticket::STATUS_OPEN)
        ->and($ticket->user_id)->toBe($this->user->id);

    $this->assertDatabaseHas('support_tickets', [
        'subject' => 'Test Subject',
        'message' => 'Test message content',
    ]);
});

test('can check ticket status', function () {
    $openTicket = Ticket::create([
        'subject' => 'Open Ticket',
        'message' => 'This is an open ticket',
        'status' => Ticket::STATUS_OPEN,
        'user_id' => $this->user->id,
        'user_type' => get_class($this->user),
    ]);
    
    $closedTicket = Ticket::create([
        'subject' => 'Closed Ticket',
        'message' => 'This is a closed ticket',
        'status' => Ticket::STATUS_CLOSED,
        'user_id' => $this->user->id,
        'user_type' => get_class($this->user),
    ]);

    expect($openTicket->isOpen())->toBeTrue()
        ->and($openTicket->isClosed())->toBeFalse()
        ->and($closedTicket->isOpen())->toBeFalse()
        ->and($closedTicket->isClosed())->toBeTrue();
}); 