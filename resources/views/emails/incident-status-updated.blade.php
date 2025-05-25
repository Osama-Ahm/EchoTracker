<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Status Updated</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2d5a27;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2d5a27;
            margin-bottom: 10px;
        }
        .status-update {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #2d5a27;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
        }
        .status-reported { background-color: #dc3545; }
        .status-under_review { background-color: #ffc107; color: #000; }
        .status-in_progress { background-color: #0d6efd; }
        .status-resolved { background-color: #198754; }
        .status-closed { background-color: #6c757d; }
        .incident-details {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2d5a27;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🌍 EcoTracker</div>
            <h2>Incident Status Updated</h2>
        </div>

        <p>Hello {{ $incident->user->name ?? 'User' }},</p>

        <p>We wanted to let you know that the status of your environmental incident report has been updated.</p>

        <div class="status-update">
            <h3>Status Change</h3>
            <p>
                <strong>From:</strong> 
                <span class="status-badge status-{{ $oldStatus }}">
                    {{ str_replace('_', ' ', $oldStatus) }}
                </span>
            </p>
            <p>
                <strong>To:</strong> 
                <span class="status-badge status-{{ $newStatus }}">
                    {{ str_replace('_', ' ', $newStatus) }}
                </span>
            </p>
        </div>

        <div class="incident-details">
            <h3>{{ $incident->title }}</h3>
            <p><strong>Report ID:</strong> #{{ $incident->id }}</p>
            <p><strong>Category:</strong> {{ $incident->category->name ?? 'N/A' }}</p>
            <p><strong>Priority:</strong> {{ ucfirst($incident->priority) }}</p>
            @if($incident->address)
                <p><strong>Location:</strong> {{ $incident->address }}</p>
            @endif
            <p><strong>Reported on:</strong> {{ $incident->created_at->format('F j, Y \a\t g:i A') }}</p>
            
            @if($incident->admin_notes)
                <div style="margin-top: 15px; padding: 10px; background-color: #e9ecef; border-radius: 5px;">
                    <strong>Admin Notes:</strong><br>
                    {{ $incident->admin_notes }}
                </div>
            @endif
        </div>

        @if($newStatus === 'resolved')
            <div style="background-color: #d1edcc; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #198754; margin: 0 0 10px 0;">🎉 Great News!</h4>
                <p style="margin: 0;">Your incident has been resolved! Thank you for helping make our environment better.</p>
            </div>
        @elseif($newStatus === 'in_progress')
            <div style="background-color: #cce7ff; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #0d6efd; margin: 0 0 10px 0;">🔧 Work in Progress</h4>
                <p style="margin: 0;">Your incident is now being actively worked on. We'll keep you updated on the progress.</p>
            </div>
        @elseif($newStatus === 'under_review')
            <div style="background-color: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #856404; margin: 0 0 10px 0;">👀 Under Review</h4>
                <p style="margin: 0;">Your incident is being reviewed by our team. We'll update you once we have more information.</p>
            </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ route('incidents.show', $incident) }}" class="btn">View Full Report</a>
        </div>

        <p>If you have any questions or concerns about this update, please don't hesitate to contact us.</p>

        <p>Thank you for your contribution to environmental protection!</p>

        <div class="footer">
            <p>This email was sent automatically by EcoTracker.<br>
            You are receiving this because you reported an environmental incident.</p>
            <p>&copy; {{ date('Y') }} EcoTracker. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
