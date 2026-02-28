<div class="sync-details-container">
    <!-- Summary Header -->
    <div style="background: {{ $sync->status === 'completed' && $sync->failed_items == 0 ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : ($sync->status === 'failed' ? 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)' : ($sync->failed_items > 0 ? 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)' : 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)')) }}; color: #fff; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">Sync ID #{{ $sync->id }}</div>
                <div style="font-size: 24px; font-weight: 700;">{{ $sync->sync_type_label }} Sync</div>
                @if($sync->failed_items > 0)
                <div style="font-size: 13px; margin-top: 5px; opacity: 0.95;">
                    <i class="fas fa-exclamation-triangle"></i> {{ $sync->failed_items }} product(s) failed to sync
                </div>
                @endif
            </div>
            <div style="text-align: right;">
                <div style="font-size: 32px; font-weight: 700;">{{ $sync->synced_items }}<span style="font-size: 14px; opacity: 0.8;">/{{ $sync->total_items }}</span></div>
                <div style="font-size: 12px; opacity: 0.9;">Products Synced</div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
        <div style="background: #f8f9fc; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 24px; font-weight: 700; color: #10b981;">{{ $sync->synced_items }}</div>
            <div style="font-size: 12px; color: #718096;">Synced</div>
        </div>
        <div style="background: {{ $sync->failed_items > 0 ? '#fef2f2' : '#f8f9fc' }}; padding: 15px; border-radius: 8px; text-align: center; {{ $sync->failed_items > 0 ? 'border: 2px solid #fca5a5;' : '' }}">
            <div style="font-size: 24px; font-weight: 700; color: #ef4444;">{{ $sync->failed_items }}</div>
            <div style="font-size: 12px; color: {{ $sync->failed_items > 0 ? '#991b1b' : '#718096' }};">Failed</div>
        </div>
        <div style="background: #f8f9fc; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 24px; font-weight: 700; color: #f59e0b;">{{ $sync->skipped_items }}</div>
            <div style="font-size: 12px; color: #718096;">Skipped</div>
        </div>
        <div style="background: #f8f9fc; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 24px; font-weight: 700; color: #667eea;">{{ $sync->duration }}</div>
            <div style="font-size: 12px; color: #718096;">Duration</div>
        </div>
    </div>

    <!-- Meta Info -->
    <div style="background: #f8f9fc; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
            <div>
                <div style="font-size: 11px; text-transform: uppercase; color: #718096; margin-bottom: 4px;">Started At</div>
                <div style="font-weight: 600; color: #2d3748;">{{ $sync->started_at ? $sync->started_at->format('M d, Y H:i:s') : '-' }}</div>
            </div>
            <div>
                <div style="font-size: 11px; text-transform: uppercase; color: #718096; margin-bottom: 4px;">Completed At</div>
                <div style="font-weight: 600; color: #2d3748;">{{ $sync->completed_at ? $sync->completed_at->format('M d, Y H:i:s') : '-' }}</div>
            </div>
            <div>
                <div style="font-size: 11px; text-transform: uppercase; color: #718096; margin-bottom: 4px;">Trigger Type</div>
                <div style="font-weight: 600; color: #2d3748;">
                    <i class="fas {{ $sync->trigger_type === 'manual' ? 'fa-hand-pointer' : ($sync->trigger_type === 'cron' ? 'fa-clock' : 'fa-bolt') }}"></i>
                    {{ $sync->trigger_type_label }}
                </div>
            </div>
            <div>
                <div style="font-size: 11px; text-transform: uppercase; color: #718096; margin-bottom: 4px;">Triggered By</div>
                <div style="font-weight: 600; color: #2d3748;">
                    @if($sync->triggeredBy)
                        {{ $sync->triggeredBy->first_name }} {{ $sync->triggeredBy->last_name }}
                    @else
                        {{ $sync->trigger_type === 'cron' ? 'System (Cron Job)' : '-' }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($sync->status === 'failed' && $sync->error_message)
    <!-- Global Error Message -->
    <div style="background: #fee2e2; border: 2px solid #fca5a5; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <div style="display: flex; align-items: flex-start; gap: 10px;">
            <i class="fas fa-exclamation-triangle" style="color: #ef4444; margin-top: 3px; font-size: 18px;"></i>
            <div>
                <div style="font-weight: 600; color: #991b1b; margin-bottom: 4px;">Sync Failed</div>
                <div style="font-size: 13px; color: #991b1b;">{{ $sync->error_message }}</div>
            </div>
        </div>
    </div>
    @endif

    @if($sync->sync_details)
    @php $details = is_array($sync->sync_details) ? $sync->sync_details : json_decode($sync->sync_details, true); @endphp
    
    <!-- FAILED PRODUCTS FIRST - Most Important -->
    @if(!empty($details['failed']))
    <div style="margin-bottom: 20px;">
        <div style="background: #fee2e2; border: 2px solid #fca5a5; border-radius: 10px; overflow: hidden;">
            <div style="background: #ef4444; color: #fff; padding: 12px 15px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-times-circle" style="font-size: 18px;"></i>
                <span style="font-weight: 600; font-size: 14px;">
                    Failed Products ({{ count($details['failed']) }}{{ isset($details['failed_truncated']) ? '+' : '' }})
                </span>
            </div>
            <div style="max-height: 300px; overflow-y: auto; padding: 0;">
                <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
                    <thead style="background: #fef2f2; position: sticky; top: 0;">
                        <tr>
                            <th style="padding: 10px 12px; text-align: left; border-bottom: 1px solid #fca5a5; color: #991b1b;">ID</th>
                            <th style="padding: 10px 12px; text-align: left; border-bottom: 1px solid #fca5a5; color: #991b1b;">SKU</th>
                            <th style="padding: 10px 12px; text-align: left; border-bottom: 1px solid #fca5a5; color: #991b1b;">Product Name</th>
                            <th style="padding: 10px 12px; text-align: left; border-bottom: 1px solid #fca5a5; color: #991b1b; width: 40%;">Error Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details['failed'] as $index => $item)
                        <tr style="background: {{ $index % 2 == 0 ? '#fff' : '#fef2f2' }};">
                            <td style="padding: 10px 12px; border-bottom: 1px solid #fecaca;">
                                <a href="{{ url('products/' . $item['id']) }}" target="_blank" style="color: #2563eb; text-decoration: none;">
                                    #{{ $item['id'] }}
                                </a>
                            </td>
                            <td style="padding: 10px 12px; border-bottom: 1px solid #fecaca;">
                                <code style="background: #fee2e2; padding: 2px 6px; border-radius: 3px; color: #991b1b;">{{ $item['sku'] ?? '-' }}</code>
                            </td>
                            <td style="padding: 10px 12px; border-bottom: 1px solid #fecaca; color: #374151;">
                                {{ \Illuminate\Support\Str::limit($item['name'] ?? 'Unknown Product', 35) }}
                            </td>
                            <td style="padding: 10px 12px; border-bottom: 1px solid #fecaca;">
                                <div style="background: #fee2e2; padding: 8px 10px; border-radius: 5px; color: #991b1b; font-size: 11px; line-height: 1.4;">
                                    <i class="fas fa-bug" style="margin-right: 5px;"></i>
                                    {{ $item['error'] ?? 'Unknown error' }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(isset($details['failed_truncated']))
                <div style="text-align: center; padding: 12px; color: #991b1b; font-size: 11px; background: #fef2f2;">
                    <i class="fas fa-ellipsis-h"></i> ... and more items (showing first 100)
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Synced Products -->
    @if(!empty($details['synced']))
    <div style="margin-bottom: 20px;">
        <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px; overflow: hidden;">
            <div style="background: #10b981; color: #fff; padding: 12px 15px; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-check-circle" style="font-size: 18px;"></i>
                    <span style="font-weight: 600; font-size: 14px;">
                        Synced Products ({{ count($details['synced']) }}{{ isset($details['synced_truncated']) ? '+' : '' }})
                    </span>
                </div>
                <button type="button" class="btn btn-xs" style="background: rgba(255,255,255,0.2); color: #fff; border: none;" onclick="$('#synced-products-list').slideToggle();">
                    <i class="fas fa-chevron-down"></i> Toggle
                </button>
            </div>
            <div id="synced-products-list" style="max-height: 200px; overflow-y: auto; padding: 0; {{ !empty($details['failed']) ? 'display: none;' : '' }}">
                <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
                    <thead style="background: #f0fdf4; position: sticky; top: 0;">
                        <tr>
                            <th style="padding: 10px 12px; text-align: left; border-bottom: 1px solid #86efac; color: #065f46;">ID</th>
                            <th style="padding: 10px 12px; text-align: left; border-bottom: 1px solid #86efac; color: #065f46;">SKU</th>
                            <th style="padding: 10px 12px; text-align: left; border-bottom: 1px solid #86efac; color: #065f46;">Product Name</th>
                            <th style="padding: 10px 12px; text-align: left; border-bottom: 1px solid #86efac; color: #065f46;">WC ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details['synced'] as $index => $item)
                        <tr style="background: {{ $index % 2 == 0 ? '#fff' : '#f0fdf4' }};">
                            <td style="padding: 8px 12px; border-bottom: 1px solid #d1fae5;">#{{ $item['id'] }}</td>
                            <td style="padding: 8px 12px; border-bottom: 1px solid #d1fae5;">
                                <code style="background: #d1fae5; padding: 2px 6px; border-radius: 3px; color: #065f46;">{{ $item['sku'] ?? '-' }}</code>
                            </td>
                            <td style="padding: 8px 12px; border-bottom: 1px solid #d1fae5; color: #374151;">{{ \Illuminate\Support\Str::limit($item['name'], 40) }}</td>
                            <td style="padding: 8px 12px; border-bottom: 1px solid #d1fae5; color: #10b981; font-weight: 600;">{{ $item['wc_id'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(isset($details['synced_truncated']))
                <div style="text-align: center; padding: 12px; color: #065f46; font-size: 11px; background: #f0fdf4;">
                    <i class="fas fa-ellipsis-h"></i> ... and more items (showing first 100)
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if(empty($details['synced']) && empty($details['failed']))
    <div style="text-align: center; padding: 30px; color: #718096;">
        <i class="fas fa-inbox" style="font-size: 40px; margin-bottom: 10px; opacity: 0.5;"></i>
        <p>No detailed sync information available</p>
    </div>
    @endif
    @else
    <div style="text-align: center; padding: 30px; color: #718096;">
        <i class="fas fa-spinner fa-spin" style="font-size: 40px; margin-bottom: 10px; opacity: 0.5;"></i>
        <p>Sync is still in progress...</p>
    </div>
    @endif
</div>
