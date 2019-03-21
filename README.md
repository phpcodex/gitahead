# git-a-head

This project was designed to read your current Git head
(So the branch you are on matters). This will establish
all of the commits and find the links between merges.

The reason for this being created, was because when you
perform a migration command such as:

    php artisan migrate:down

you are only affecting your database, yet when you have
code changes, they still remain applied and you struggle 
to find which Git commit was the correct one to revert 
back to. Rather than having a roll-back built into this
project, that will be split out so the project must
require this and that will allow you to roll-back code
based on a certain date.

A scenario for this is with CI/CD always pushing code
forward seamlessly, you might notice an issue in
production days later (Friday release?) so you might
just want to 'roll-back YYYY-MM-DD' because that date
was perfectly fine.

Example usage:

    Commit::check();
    
This returns a `Commit` object back. In order to access 
the private data within the object, you can get the data
as follows:

    $commit = Commit::check();
    $data   = $commit->getData();