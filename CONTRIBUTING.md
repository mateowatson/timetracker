# How You Can Contribute

Timetracker is an open source project maintained by brothers [Matt](https://github.com/mateowatson) and [Blake](https://github.com/blakewatson) Watson. We welcome input in the form of filing an issue in the [issue tracker on Github](https://github.com/mateowatson/timetracker/issues). If you would like to contribute code, you may also make a pull request [through GitHub](https://github.com/mateowatson/timetracker).

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [Issue tracker](#issue-tracker)
- [Pull request (PR)](#pull-request-pr)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Issue tracker

Go to the [Github issue tracker](https://github.com/mateowatson/timetracker/issues) and click New Issue. Write a succinct but descriptive title and a description with as much specific information as possible. Generally, issues are either to report a bug, a coding error or typo, a feature request, or a question. This is the best, most organized way submit an issue, but you may also email the creator of Timetracker, Matt Watson, at matt@mattwatson.org.

## Pull request (PR)

Unless you are a regular contributor, it is not very important to follow our branching conventions. You'll probably just want to make it on master, but if you are developing something more in depth, the following general guidelines may help.

We basically follow the [gitflow](https://nvie.com/posts/a-successful-git-branching-model/) strategy for updating branches and deploying releases.

To create a new feature or to fix a `develop` branch bug, branch off of `develop` and name your branch with the `feature` prefix and a sensible name. The word "feature" here is somewhat misleading, as it can also be a "bug fix", such as `feature/sql-timediff-fix`. If the PR is in response to a specific issue, name the branch after the issue number and title (kebab case), such as `feature/40-improvements-to-start-new-project-or-task`.

To submit a critical bug fix, branch off of the branch that has the bug and prefix your new branch name with `hotfix/`.