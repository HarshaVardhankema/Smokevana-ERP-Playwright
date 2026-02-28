<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Ticket chat channel authorization
Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    // Check if user has permission to access this ticket
    // Allow: 1) Admins for the business OR 2) Commission agent assigned to the ticket
    $ticket = \App\Ticket::with('lead')->find($ticketId);
    
    if (!$ticket) {
        return false;
    }
    
    $business_id = $user->business_id;
    
    // Check if ticket belongs to user's business
    if ($ticket->lead && $ticket->lead->business_id == $business_id) {
        // Allow if user is admin OR if user is the assigned commission agent
        if ($user->hasRole('Admin#' . $business_id) || $ticket->user_id == $user->id) {
            return true;
        }
    }
    
    return false;
});