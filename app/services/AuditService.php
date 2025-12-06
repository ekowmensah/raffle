<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditService
{
    private $auditLog;

    public function __construct()
    {
        $this->auditLog = new AuditLog();
    }

    /**
     * Log user login
     */
    public function logLogin($userId, $success = true)
    {
        return $this->auditLog->logAction([
            'user_id' => $userId,
            'action' => $success ? 'user_login' : 'user_login_failed',
            'entity_type' => 'user',
            'entity_id' => $userId
        ]);
    }

    /**
     * Log user logout
     */
    public function logLogout($userId)
    {
        return $this->auditLog->logAction([
            'user_id' => $userId,
            'action' => 'user_logout',
            'entity_type' => 'user',
            'entity_id' => $userId
        ]);
    }

    /**
     * Log draw conducted
     */
    public function logDrawConducted($userId, $drawId, $campaignName, $winnersCount)
    {
        return $this->auditLog->logAction([
            'user_id' => $userId,
            'action' => 'draw_conducted',
            'entity_type' => 'draw',
            'entity_id' => $drawId,
            'new_values' => [
                'campaign' => $campaignName,
                'winners_count' => $winnersCount,
                'conducted_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Log winner selection
     */
    public function logWinnerSelected($userId, $drawId, $winnerData)
    {
        return $this->auditLog->logAction([
            'user_id' => $userId,
            'action' => 'winner_selected',
            'entity_type' => 'draw_winner',
            'entity_id' => $winnerData['winner_id'] ?? null,
            'new_values' => $winnerData
        ]);
    }

    /**
     * Log payment processed
     */
    public function logPaymentProcessed($paymentId, $paymentData)
    {
        return $this->auditLog->logAction([
            'user_id' => null, // System action
            'action' => 'payment_processed',
            'entity_type' => 'payment',
            'entity_id' => $paymentId,
            'new_values' => $paymentData
        ]);
    }

    /**
     * Log campaign created
     */
    public function logCampaignCreated($userId, $campaignId, $campaignData)
    {
        return $this->auditLog->logAction([
            'user_id' => $userId,
            'action' => 'campaign_created',
            'entity_type' => 'campaign',
            'entity_id' => $campaignId,
            'new_values' => $campaignData
        ]);
    }

    /**
     * Log campaign updated
     */
    public function logCampaignUpdated($userId, $campaignId, $oldData, $newData)
    {
        return $this->auditLog->logAction([
            'user_id' => $userId,
            'action' => 'campaign_updated',
            'entity_type' => 'campaign',
            'entity_id' => $campaignId,
            'old_values' => $oldData,
            'new_values' => $newData
        ]);
    }

    /**
     * Log campaign deleted
     */
    public function logCampaignDeleted($userId, $campaignId, $campaignData)
    {
        return $this->auditLog->logAction([
            'user_id' => $userId,
            'action' => 'campaign_deleted',
            'entity_type' => 'campaign',
            'entity_id' => $campaignId,
            'old_values' => $campaignData
        ]);
    }

    /**
     * Log configuration change
     */
    public function logConfigurationChanged($userId, $configKey, $oldValue, $newValue)
    {
        return $this->auditLog->logAction([
            'user_id' => $userId,
            'action' => 'configuration_changed',
            'entity_type' => 'configuration',
            'entity_id' => null,
            'old_values' => [$configKey => $oldValue],
            'new_values' => [$configKey => $newValue]
        ]);
    }

    /**
     * Log user created
     */
    public function logUserCreated($creatorId, $newUserId, $userData)
    {
        return $this->auditLog->logAction([
            'user_id' => $creatorId,
            'action' => 'user_created',
            'entity_type' => 'user',
            'entity_id' => $newUserId,
            'new_values' => $userData
        ]);
    }

    /**
     * Log user updated
     */
    public function logUserUpdated($updaterId, $userId, $oldData, $newData)
    {
        return $this->auditLog->logAction([
            'user_id' => $updaterId,
            'action' => 'user_updated',
            'entity_type' => 'user',
            'entity_id' => $userId,
            'old_values' => $oldData,
            'new_values' => $newData
        ]);
    }

    /**
     * Log user deleted
     */
    public function logUserDeleted($deleterId, $userId, $userData)
    {
        return $this->auditLog->logAction([
            'user_id' => $deleterId,
            'action' => 'user_deleted',
            'entity_type' => 'user',
            'entity_id' => $userId,
            'old_values' => $userData
        ]);
    }

    /**
     * Log ticket generated
     */
    public function logTicketGenerated($ticketId, $ticketData)
    {
        return $this->auditLog->logAction([
            'user_id' => null, // System action
            'action' => 'ticket_generated',
            'entity_type' => 'ticket',
            'entity_id' => $ticketId,
            'new_values' => $ticketData
        ]);
    }

    /**
     * Log prize payment
     */
    public function logPrizePayment($userId, $winnerId, $paymentData)
    {
        return $this->auditLog->logAction([
            'user_id' => $userId,
            'action' => 'prize_paid',
            'entity_type' => 'draw_winner',
            'entity_id' => $winnerId,
            'new_values' => $paymentData
        ]);
    }

    /**
     * Generic log method
     */
    public function log($action, $entityType = null, $entityId = null, $data = [])
    {
        return $this->auditLog->logAction([
            'user_id' => $_SESSION['user_id'] ?? null,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'new_values' => $data
        ]);
    }
}
