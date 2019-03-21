<?php

namespace phpcodex\Gitahead;

use phpcodex\Gitahead\Models\GitCommit;

/**
 * Class Commit
 *
 * This package will read the HEAD and track all of the
 * commits before it and organise a pointer
 * reference so you can see a clear
 * timeline on commits.
 *
 * @package phpcodex\Gitahead
 */
class Commit
{

    /**
     * The data from the git commits will be stored
     * in this array.
     *
     * @var array
     */
    private $data       = [];

    /**
     * A single commit may have multiple lines of
     * messages, this is only intended as
     * a place-holder for commits.
     *
     * @var array
     */
    private $messages   = [];

    /**
     * This is a place-holder for the commit we
     * are currently parsing.
     *
     * @var null
     */
    private $commit     = null;

    /**
     * A const for the short-hand version of
     * the full commit hash.
     */
    const GIT_HASH_SHORT  = 7;

    /**
     * A const similar to the short hash, this
     * is the full hash key.
     */
    const GIT_HASH_LENGTH = 41;

    /**
     * A const for the verb we are looking for
     * which is for the commit hash.
     */
    const GIT_VERB_COMMIT = 'commit ';

    /**
     * A const for the verb we are looking for
     * which is for the author description.
     */
    const GIT_VERB_AUTHOR = 'Author: ';

    /**
     * A const for the verb we are looking for
     * which is the date the commit
     * was applied.
     */
    const GIT_VERB_DATE   = 'Date: ';

    /**
     * A const for the verb we are looking for
     * which is the A..B Merge
     */
    const GIT_VERB_MERGE  = 'Merge: ';

    /**
     * A const for how we will re-display the
     * Author as an output from this
     * package
     */
    const COMMIT_VERB_AUTHOR = 'Author';

    /**
     * A const for how we will re-display the
     * Date as an output from this
     * package
     */
    const COMMIT_VERB_DATE   = 'Date';

    /**
     * A const for how we will re-display the
     * Merge as an output from this
     * package
     */
    const COMMIT_VERB_MERGE  = 'Merge';

    /**
     * A const for how we will re-display the
     * Commit as an output from this
     * package
     */
    const COMMIT_VERB_COMMIT = 'Commit';

    /**
     * Commit constructor.
     * 
     * @param array $logs
     */
    public function __construct(array $logs)
    {
        $this->validateLogs($logs);
    }

    public function getData() : array
    {
        return $this->data;
    }

    public static function check($commit_hash = null) : Commit
    {

        preg_match('/([a-z0-9]+)/', $commit_hash, $matches);
        $commit_hash = $matches[0] ?? null;

        if ($commit_hash === null) {
            exec('git --no-pager log', $logs);
        } else {
            exec('git --no-pager log ' . $commit_hash . '..HEAD', $logs);
        }

        return new self($logs);

    }

    private function getVerbs() : array
    {
        return [
            self::COMMIT_VERB_AUTHOR => self::GIT_VERB_AUTHOR,
            self::COMMIT_VERB_DATE   => self::GIT_VERB_DATE,
            self::COMMIT_VERB_MERGE  => self::GIT_VERB_MERGE,
        ];
    }

    private function validateLogs(array $logs) : void
    {
        $verbs = $this->getVerbs();

        while (true) {
            $row = array_shift($logs);

            $isMessage = true;

            if (substr($row, 0, strlen(self::GIT_VERB_COMMIT)) === self::GIT_VERB_COMMIT) {
                if ($this->commit !== null) {
                    $this->collateMessages();
                }

                $gitCommit = new GitCommit;
                $gitCommit->setCommit(substr($row, strlen(self::GIT_VERB_COMMIT), strlen($row)));

                $this->commit = substr($row, strlen(self::GIT_VERB_COMMIT), self::GIT_HASH_SHORT);
                $this->data[$this->commit] = $gitCommit;
                continue;
            }

            if ($this->commit !== null) {
                foreach ($verbs as $verbName => $verbToSearch) {
                    if (substr($row, 0, strlen($verbToSearch)) === $verbToSearch) {

                        $setter = 'set' . $verbName;

                        if (method_exists($this->data[$this->commit], $setter)) {
                            $this->data[$this->commit]->$setter(trim(substr($row, strlen($verbToSearch), strlen($row))));
                            $isMessage = false;
                            continue;
                        }

                    }
                }

                if ($isMessage && !empty(trim($row))) {
                    $this->messages[] = trim($row);
                }
            }

            if (empty($logs)) {
                break;
            }
        }

        $this->collateMessages();
    }

    private function collateMessages() : void
    {
        $this->data[$this->commit]->setMessage(implode(PHP_EOL, $this->messages));
        $this->messages = [];
    }
}
