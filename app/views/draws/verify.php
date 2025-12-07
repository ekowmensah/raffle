<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draw Verification - Transparency Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 3rem;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .verification-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
        }

        .status-banner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .status-banner.verified {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .status-banner.failed {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .status-banner i {
            font-size: 2rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-box {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid #667eea;
        }

        .info-box label {
            display: block;
            font-size: 0.85rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .info-box .value {
            font-size: 1.1rem;
            color: #111827;
            font-weight: 600;
            word-break: break-all;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: #667eea;
        }

        .winners-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }

        .winners-table thead th {
            background: #667eea;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .winners-table thead th:first-child {
            border-radius: 10px 0 0 10px;
        }

        .winners-table thead th:last-child {
            border-radius: 0 10px 10px 0;
        }

        .winners-table tbody tr {
            background: #f9fafb;
            transition: all 0.3s;
        }

        .winners-table tbody tr:hover {
            background: #f3f4f6;
            transform: translateX(5px);
        }

        .winners-table tbody td {
            padding: 1rem;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }

        .winners-table tbody td:first-child {
            border-left: 1px solid #e5e7eb;
            border-radius: 10px 0 0 10px;
        }

        .winners-table tbody td:last-child {
            border-right: 1px solid #e5e7eb;
            border-radius: 0 10px 10px 0;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-weight: 700;
            color: white;
        }

        .rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
        .rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); }
        .rank-3 { background: linear-gradient(135deg, #fb923c, #f97316); }
        .rank-other { background: linear-gradient(135deg, #667eea, #764ba2); }

        .weight-bar {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.25rem;
        }

        .weight-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.5s ease;
        }

        .decay-rules {
            background: #fef3c7;
            border: 2px solid #fbbf24;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .decay-rules h4 {
            color: #92400e;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .decay-rules ul {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .decay-rules li {
            color: #78350f;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .decay-rules li i {
            color: #f59e0b;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            color: #667eea;
            padding: 1rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .loading {
            text-align: center;
            padding: 4rem;
            color: white;
        }

        .loading i {
            font-size: 3rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .error-message {
            background: #fee2e2;
            border: 2px solid #ef4444;
            color: #991b1b;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .verification-card {
                padding: 1.5rem;
            }

            .winners-table {
                font-size: 0.85rem;
            }

            .winners-table thead th,
            .winners-table tbody td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shield-check"></i> Draw Verification</h1>
            <p>Independent verification of draw fairness and transparency</p>
        </div>

        <div id="loading" class="loading">
            <i class="fas fa-spinner"></i>
            <p style="margin-top: 1rem;">Verifying draw results...</p>
        </div>

        <div id="content" style="display: none;"></div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="<?= url('draw') ?>" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Draws
            </a>
        </div>
    </div>

    <script>
        const drawId = <?= $draw_id ?? 'null' ?>;

        if (!drawId) {
            document.getElementById('loading').innerHTML = '<div class="error-message"><i class="fas fa-exclamation-triangle"></i><h3>Invalid Draw ID</h3></div>';
        } else {
            fetchVerificationData();
        }

        async function fetchVerificationData() {
            try {
                const response = await fetch(`<?= url('draw/verify/') ?>${drawId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                if (data.error) {
                    showError(data.error);
                    return;
                }

                displayVerificationReport(data);
            } catch (error) {
                showError('Failed to load verification data: ' + error.message);
            }
        }

        function displayVerificationReport(data) {
            const isVerified = data.verification.is_verifiable;
            
            const html = `
                <div class="verification-card">
                    <div class="status-banner ${isVerified ? 'verified' : 'failed'}">
                        <i class="fas fa-${isVerified ? 'check-circle' : 'times-circle'}"></i>
                        <span>${isVerified ? '✓ Draw Verified Successfully' : '✗ Verification Failed'}</span>
                    </div>

                    <div class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Draw Information
                    </div>

                    <div class="info-grid">
                        <div class="info-box">
                            <label>Draw ID</label>
                            <div class="value">#${data.draw_id}</div>
                        </div>
                        <div class="info-box">
                            <label>Draw Date</label>
                            <div class="value">${new Date(data.draw_date).toLocaleDateString()}</div>
                        </div>
                        <div class="info-box">
                            <label>Draw Type</label>
                            <div class="value">${data.draw_type.toUpperCase()}</div>
                        </div>
                        <div class="info-box">
                            <label>Status</label>
                            <div class="value">${data.status.toUpperCase()}</div>
                        </div>
                        <div class="info-box">
                            <label>Eligible Tickets</label>
                            <div class="value">${data.draw_data.eligible_tickets.toLocaleString()}</div>
                        </div>
                        <div class="info-box">
                            <label>Total Prize Pool</label>
                            <div class="value">GHS ${parseFloat(data.draw_data.total_prize_pool).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                        </div>
                    </div>

                    <div class="section-title">
                        <i class="fas fa-key"></i>
                        Cryptographic Verification
                    </div>

                    <div class="info-grid">
                        <div class="info-box" style="grid-column: span 2;">
                            <label>Random Seed (Cryptographic)</label>
                            <div class="value" style="font-family: monospace; font-size: 0.9rem;">${data.draw_data.random_seed}</div>
                        </div>
                        <div class="info-box" style="grid-column: span 2;">
                            <label>Tamper-Proof Seal (SHA-256 Hash)</label>
                            <div class="value" style="font-family: monospace; font-size: 0.9rem;">${data.draw_data.verification_hash}</div>
                        </div>
                        <div class="info-box">
                            <label>Draw Sealed</label>
                            <div class="value">${data.verification.hash_matches ? '✓ Yes' : '✗ No'}</div>
                        </div>
                        <div class="info-box">
                            <label>Winners Valid</label>
                            <div class="value">${data.verification.winners_match ? '✓ Yes' : '✗ No'}</div>
                        </div>
                    </div>
                    
                    <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                        <p style="color: #1e40af; margin: 0; font-size: 0.9rem;">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Verification Explained:</strong> The random seed and hash are cryptographically generated during the draw to ensure fairness. 
                            The presence of both values confirms the draw was properly sealed and cannot be tampered with after completion.
                        </p>
                    </div>
                </div>

                <div class="verification-card">
                    <div class="section-title">
                        <i class="fas fa-trophy"></i>
                        Winners (${data.winners.length})
                    </div>

                    <table class="winners-table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Ticket Code</th>
                                <th>Quantity</th>
                                <th>Age (Days)</th>
                                <th>Weight</th>
                                <th>Weighted Qty</th>
                                <th>Prize Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.winners.map(winner => `
                                <tr>
                                    <td>
                                        <span class="rank-badge rank-${winner.rank <= 3 ? winner.rank : 'other'}">
                                            ${winner.rank}
                                        </span>
                                    </td>
                                    <td><strong>${winner.ticket_code}</strong></td>
                                    <td>${winner.ticket_quantity}</td>
                                    <td>${winner.ticket_age_days}</td>
                                    <td>
                                        ${(winner.weight_multiplier * 100).toFixed(0)}%
                                        <div class="weight-bar">
                                            <div class="weight-fill" style="width: ${winner.weight_multiplier * 100}%"></div>
                                        </div>
                                    </td>
                                    <td>${winner.weighted_quantity}</td>
                                    <td><strong>GHS ${parseFloat(winner.prize_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>

                    <div class="decay-rules">
                        <h4><i class="fas fa-clock"></i> Time Decay Rules</h4>
                        <ul>
                            ${Object.entries(data.time_decay_rules).map(([key, value]) => `
                                <li><i class="fas fa-check"></i> ${key.replace(/_/g, ' ')}: ${value}</li>
                            `).join('')}
                        </ul>
                    </div>
                </div>
            `;

            document.getElementById('loading').style.display = 'none';
            document.getElementById('content').style.display = 'block';
            document.getElementById('content').innerHTML = html;
        }

        function showError(message) {
            document.getElementById('loading').innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <h3>Error</h3>
                    <p>${message}</p>
                </div>
            `;
        }
    </script>
</body>
</html>
