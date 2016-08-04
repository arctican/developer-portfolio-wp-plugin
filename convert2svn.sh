# Make sure we're in the folder
cd "$(dirname "$0")"

SVN_DIR=./svn_wp/developer-portfolio
SVN_TRUNK=./svn_wp/developer-portfolio/trunk

# Clear the trunk, copy the new files over, and add to trunk (will get errors as already there)
rm -R $SVN_TRUNK/*
rsync -av --exclude='.git' --exclude='.gitignore' --exclude='convert2svn.sh' --exclude='svn_wp' ./ $SVN_TRUNK
#svn add trunk/*

# Get the latest git tag
GIT_TAG=$(git describe --abbrev=0 --tags)

# Check in changes and tag new version
cd $SVN_DIR
svn ci -m "Adding changes for version $GIT_TAG"
svn cp trunk tags/$GIT_TAG
svn ci -m "Tagging version $GIT_TAG"

# TODO check if stable tag matches
