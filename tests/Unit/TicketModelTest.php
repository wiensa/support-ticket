<?php

use Herd\SupportTicket\Models\Ticket;
use Herd\SupportTicket\Models\TicketReply;

test('ticket has the correct fillable attributes', function () {
    $ticket = new Ticket();
    
    expect($ticket->getFillable())->toContain('subject')
        ->and($ticket->getFillable())->toContain('message')
        ->and($ticket->getFillable())->toContain('status')
        ->and($ticket->getFillable())->toContain('user_id')
        ->and($ticket->getFillable())->toContain('user_type');
});

test('ticket uses uuid as primary key', function () {
    $ticket = new Ticket();
    
    expect($ticket)->toBeInstanceOf(\Illuminate\Database\Eloquent\Model::class)
        ->and($ticket->getIncrementing())->toBeFalse()
        ->and($ticket->getKeyType())->toBe('string');
});

test('ticket has correct table name', function () {
    $ticket = new Ticket();
    
    expect($ticket->getTable())->toBe('support_tickets');
});

test('ticket has correct status constants', function () {
    expect(Ticket::STATUS_OPEN)->toBe('open')
        ->and(Ticket::STATUS_PENDING)->toBe('pending')
        ->and(Ticket::STATUS_RESOLVED)->toBe('resolved')
        ->and(Ticket::STATUS_CLOSED)->toBe('closed');
});

test('ticket has replies relationship', function () {
    $ticket = new Ticket();
    $relation = $ticket->replies();
    
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($relation->getRelated())->toBeInstanceOf(TicketReply::class);
}); 