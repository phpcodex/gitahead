<?php

namespace phpcodex\Gitahead\Models;

/**
 * Class GitCommit
 *
 * This is a commit message model. This will map general
 * used key-value pairs.
 *
 * @package phpcodex\Gitahead\Models
 */
class GitCommit
{
    const GIT_MERGE_FROM = 'from';
    const GIT_MERGE_TO   = 'to';

    protected $Commit   = null;
    protected $Author   = null;
    protected $Date     = null;
    protected $Message  = null;
    protected $Merge    = null;

    public function setCommit($commit)
    {
        $this->Commit = $commit;
    }

    public function setAuthor($author)
    {
        $this->Author = $author;
    }

    public function setDate($date)
    {
        $this->Date = $date;
    }

    public function setMessage($message)
    {
        $this->Message = $message;
    }

    public function setMerge($merge)
    {
        list($a, $b) = explode(' ', $merge);
        $this->Merge = [self::GIT_MERGE_FROM => $a, self::GIT_MERGE_TO => $b];
    }
}