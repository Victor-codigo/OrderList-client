
# ------------------- BASH CUSTOM --------------------- #
parse_git_branch() {
    git branch 2> /dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/git: \1 /'
}
export PS1='${debian_chroot:+($debian_chroot)}\[\033[01;32m\]\u@\h\[\033[00;37m\]:\[\033[01;34m\]\w \[\033[01;37m\]| \[\033[01;33m\]$(parse_git_branch)\[\033[00m\]\$ '
export XDEBUG_SESSION=VSCODE

alias sf="bin/console"
alias pu="bin/phpunit --coverage-clover coverage.xml --configuration phpunit.xml.dist"
alias puf="bin/phpunit --coverage-clover coverage.xml --configuration phpunit.xml.dist --filter"
alias puhtml="bin/phpunit --configuration phpunit.xml.dist --coverage-html public/code-coverage"

# ----------------------------------------------------- #
