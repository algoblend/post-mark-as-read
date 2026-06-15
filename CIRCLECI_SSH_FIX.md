# Fixing CircleCI SSH Permission Error

## Problem
```
git@github.com: Permission denied (publickey).
fatal: Could not read from remote repository.
```

This means CircleCI doesn't have permission to clone your repository.

## Solution: Set Up CircleCI Project

### Step 1: Access CircleCI Dashboard
1. Go to [CircleCI Dashboard](https://app.circleci.com/)
2. Log in with your GitHub account

### Step 2: Set Up Project

**If project doesn't exist yet:**
1. Click "Projects" in the left sidebar
2. Find "algoblend/post-mark-as-read" in the list
3. Click "Set Up Project" button
4. CircleCI will automatically configure GitHub integration
5. Click "Start Building"

**If project already exists:**
1. Go to Project Settings
2. Verify GitHub integration is enabled
3. Check SSH keys are configured

### Step 3: Verify GitHub Integration

1. In CircleCI project settings, go to "SSH Keys"
2. You should see either:
   - **User Key** - CircleCI will use your GitHub permissions
   - **Deploy Key** - CircleCI has read-only access
   
3. If no keys are present, click "Add User Key" or "Add Deploy Key"

### Step 4: Verify Permissions

Make sure CircleCI has permission to access your repository:
1. Go to GitHub repository settings
2. Go to "Integrations" → "Applications"
3. Find CircleCI and ensure it has access

## Alternative: Use HTTPS Instead of SSH

If SSH continues to cause issues, you can configure CircleCI to use HTTPS:

1. Go to CircleCI Project Settings
2. Navigate to "Advanced Settings"
3. Enable "Only build pull requests" if you want to limit builds
4. The checkout will use HTTPS automatically

## Quick Fix: Re-setup the Project

The fastest solution:
1. Go to https://app.circleci.com/projects/project-dashboard/github/algoblend/
2. Find your project
3. Click the 3-dot menu → "Project Settings"
4. Go to "Advanced" → Click "Stop Building"
5. Go back to Projects and "Set Up Project" again
6. This will reconfigure all permissions

## Verify It's Working

After setup, trigger a build:
1. Push a commit to your branch
2. Go to CircleCI dashboard
3. You should see the build start successfully
4. The checkout step should complete without errors

## Common Issues

### "Project not found"
- Make sure you've authorized CircleCI in your GitHub account
- Check that the repository isn't private (or CircleCI has access to private repos)

### "No such host"
- This is a network issue, usually temporary
- Try re-running the build

### "Permission denied"
- Follow the steps above to add User Key or Deploy Key
- Make sure CircleCI OAuth app has access in GitHub settings

## Testing

Once configured, you should see:
```
Cloning into '.'...
Receiving objects: 100% (123/123), done.
Resolving deltas: 100% (45/45), done.
```

Instead of the permission denied error.

## Need Help?

- CircleCI Docs: https://circleci.com/docs/github-integration
- SSH Keys Guide: https://circleci.com/docs/add-ssh-key
- GitHub OAuth Apps: https://github.com/settings/applications
