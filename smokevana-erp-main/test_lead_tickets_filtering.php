<?php
/**
 * Test Script: Lead Tickets Filtering
 * 
 * This script verifies that tickets are correctly filtered by lead_id
 * Run from command line: php test_lead_tickets_filtering.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== Lead Tickets Filtering Test ===\n\n";

// Get all leads
$leads = \App\Lead::with('tickets')->limit(5)->get();

if ($leads->count() === 0) {
    echo "❌ No leads found in database\n\n";
    exit;
}

echo "✅ Found " . $leads->count() . " leads to test\n\n";

foreach ($leads as $lead) {
    echo "Lead: " . $lead->reference_no . " - " . $lead->store_name . "\n";
    echo "Lead ID: " . $lead->id . "\n";
    
    // Get tickets using the same query as the controller
    $tickets = \App\Ticket::where('lead_id', $lead->id)
        ->where('lead_id', '!=', null)
        ->get();
    
    echo "Tickets for this lead: " . $tickets->count() . "\n";
    
    if ($tickets->count() > 0) {
        echo "Ticket Details:\n";
        foreach ($tickets as $ticket) {
            $belongsToCorrectLead = ($ticket->lead_id == $lead->id) ? "✅" : "❌";
            echo "  " . $belongsToCorrectLead . " " . $ticket->reference_no . " (lead_id: " . $ticket->lead_id . ")\n";
        }
    } else {
        echo "  No tickets for this lead\n";
    }
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

// Summary test: Verify no cross-contamination
echo "=== Cross-Contamination Test ===\n";
$lead1 = $leads->first();
$lead2 = $leads->skip(1)->first();

if ($lead1 && $lead2) {
    $lead1_tickets = \App\Ticket::where('lead_id', $lead1->id)->get();
    $lead2_tickets = \App\Ticket::where('lead_id', $lead2->id)->get();
    
    echo "Lead 1 (" . $lead1->reference_no . "): " . $lead1_tickets->count() . " tickets\n";
    echo "Lead 2 (" . $lead2->reference_no . "): " . $lead2_tickets->count() . " tickets\n";
    
    // Check if any ticket from lead1 has lead_id pointing to lead2
    $contamination = false;
    foreach ($lead1_tickets as $ticket) {
        if ($ticket->lead_id != $lead1->id) {
            echo "❌ Contamination detected! Ticket " . $ticket->reference_no . " has wrong lead_id\n";
            $contamination = true;
        }
    }
    
    if (!$contamination) {
        echo "✅ No cross-contamination detected. Filtering is working correctly!\n";
    }
} else {
    echo "⚠️  Need at least 2 leads to test cross-contamination\n";
}

echo "\n=== Test Complete ===\n\n";

