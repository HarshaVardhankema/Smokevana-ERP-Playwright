@extends('layouts.app')
@section('title', 'Inventory Valuation History')

@section('css')
<style>
/* Inventory Valuation History - Professional Purple Theme */
.ivh-page {
    background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
    min-height: 100vh;
    padding-bottom: 40px;
}

.ivh-header-banner {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 8px 30px rgba(124, 58, 237, 0.25);
}

.ivh-header-content h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #fff !important;
}

.ivh-header-content .subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9) !important;
    margin: 0;
}

.ivh-header-actions .btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 10px 20px;
    font-size: 14px;
    border: none;
    background: rgba(255,255,255,0.95);
    color: #7c3aed;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.ivh-header-actions .btn:hover {
    background: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.ivh-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    overflow: hidden;
}

.ivh-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f3f4f6;
    background: linear-gradient(135deg, #faf5ff 0%, #f5f3ff 100%);
}

.ivh-card-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1e1b4b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ivh-card-body {
    padding: 24px;
}
</style>
@endsection

@section('content')
<section class="content ivh-page">
    
    <!-- Header Banner -->
    <div class="ivh-header-banner">
        <div class="ivh-header-content">
            <h1><i class="fas fa-history"></i> Valuation History</h1>
            <p class="subtitle">View all inventory valuation records</p>
        </div>
        <div class="ivh-header-actions">
            <a href="{{ route('bookkeeping.inventory.index') }}" class="btn">
                <i class="fas fa-arrow-left"></i> Back to Valuation
            </a>
        </div>
    </div>

    <!-- History Table -->
    <div class="ivh-card">
        <div class="ivh-card-header">
            <h3><i class="fas fa-list"></i> All Valuations</h3>
        </div>
        <div class="ivh-card-body">
            <table class="table table-striped" id="valuation_history_table" width="100%">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Units</th>
                        <th>Cost Value</th>
                        <th>Retail Value</th>
                        <th>Created By</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var table = $('#valuation_history_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('bookkeeping.inventory.history') }}",
            type: 'GET'
        },
        columns: [
            { data: 'valuation_date', name: 'valuation_date' },
            { 
                data: 'valuation_method', 
                name: 'valuation_method',
                render: function(data) {
                    var methods = {
                        'fifo': 'FIFO',
                        'lifo': 'LIFO',
                        'weighted_average': 'Weighted Average',
                        'specific_identification': 'Specific Identification'
                    };
                    return methods[data] || data;
                }
            },
            { 
                data: 'total_units', 
                name: 'total_units',
                render: function(data) {
                    return parseFloat(data || 0).toLocaleString('en-US', {maximumFractionDigits: 0});
                }
            },
            { data: 'total_cost_value', name: 'total_cost_value' },
            { data: 'total_retail_value', name: 'total_retail_value' },
            { data: 'created_by_name', name: 'created_by_name' },
            { 
                data: 'notes', 
                name: 'notes',
                render: function(data) {
                    return data || '-';
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            search: '<i class="fas fa-search"></i>',
            searchPlaceholder: 'Search valuations...',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ valuations',
            paginate: { 
                previous: '<i class="fas fa-chevron-left"></i>', 
                next: '<i class="fas fa-chevron-right"></i>' 
            },
            emptyTable: '<div style="text-align:center;padding:40px;"><i class="fas fa-inbox" style="font-size:48px;color:#ddd6fe;margin-bottom:16px;"></i><h4>No Valuations Found</h4><p>Start by calculating your first inventory valuation.</p></div>'
        }
    });
});
</script>
@endsection
