<?php

function create_issue($conn, $email, $scooter_id, $title, $description)
{
    $query = "INSERT INTO issues (user_email, scooter_id, title, description, status, created_at)
        VALUES ($1, $2, $3, $4, 'open', NOW())";

    $result1 = pg_prepare($conn, "create_issue", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "create_issue", array($email, $scooter_id, $title, $description));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }
}

class IssueInfo
{
    public $id;
    public $user_email;
    public $scooter_id;
    public $title;
    public $description;

    public $status;
    public $created_at;
}

function get_issues_info($conn)
{
    $query = "SELECT id, user_email, scooter_id, title, description, status, created_at
        FROM issues
        ORDER BY created_at DESC";

    $result1 = pg_prepare($conn, "get_issues_info", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "get_issues_info", array());
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $issues_info = array();
    while ($row = pg_fetch_array($result2, null, PGSQL_ASSOC)) {
        $issue_info = new IssueInfo();
        $issue_info->id = $row['id'];
        $issue_info->user_email = $row['user_email'];
        $issue_info->scooter_id = $row['scooter_id'];
        $issue_info->title = $row['title'];
        $issue_info->description = $row['description'];
        $issue_info->status = $row['status'];
        $issue_info->created_at = $row['created_at'];

        array_push($issues_info, $issue_info);
    }

    return $issues_info;
}

class IssueNotFoundException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

function delete_issue($conn, $issue_id)
{
    $query = "DELETE FROM issues
        WHERE id=$1";

    $result1 = pg_prepare($conn, "delete_issue", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "delete_issue", array($issue_id));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }
}

function update_issue_status_as_accepted($conn, $issue_id)
{
    $query = "UPDATE issues
        SET status='accepted'
        WHERE id=$1";

    $result1 = pg_prepare($conn, "update_issue_status", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "update_issue_status", array($issue_id));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }
}

?>