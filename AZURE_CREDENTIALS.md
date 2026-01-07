# Azure Web App Deployment Credentials

## App Details
- **App Name**: lovecrafted-app-2025
- **Resource Group**: CloudComputing
- **URL**: https://lovecrafted-app-2025.azurewebsites.net

## Deployment Credentials (for Jenkins)

### Method 1: Zip Deploy (Recommended)
```
Publish URL: lovecrafted-app-2025.scm.azurewebsites.net:443
Username: $lovecrafted-app-2025
Password: QyfslrJ4hbzqYsJd7AmTBh5oarZuFEjLnfQmsZANrFTnK6u5giZFkoalebbn
```

### Method 2: FTP Deploy
```
FTP URL: ftps://waws-prod-sg1-063.ftp.azurewebsites.windows.net/site/wwwroot
Username: lovecrafted-app-2025\$lovecrafted-app-2025
Password: QyfslrJ4hbzqYsJd7AmTBh5oarZuFEjLnfQmsZANrFTnK6u5giZFkoalebbn
```

### Method 3: Web Deploy (MSDeploy)
```
Publish URL: lovecrafted-app-2025.scm.azurewebsites.net:443
Site Name: lovecrafted-app-2025
Username: $lovecrafted-app-2025
Password: QyfslrJ4hbzqYsJd7AmTBh5oarZuFEjLnfQmsZANrFTnK6u5giZFkoalebbn
```

## Jenkins Credentials Setup

### Step 1: Add Credentials in Jenkins
1. Jenkins Dashboard → Manage Jenkins → Credentials
2. Add Credentials → **Username with password**
3. Fill in:
   - **Username**: `$lovecrafted-app-2025`
   - **Password**: `QyfslrJ4hbzqYsJd7AmTBh5oarZuFEjLnfQmsZANrFTnK6u5giZFkoalebbn`
   - **ID**: `azure-lovecrafted-deploy`
   - **Description**: Azure LoveCrafted Web App Deploy

### Step 2: Use in Jenkinsfile
Reference the credential ID: `azure-lovecrafted-deploy`

## Environment Variables on Azure

Already configured:
```bash
MIDTRANS_ENV=sandbox
MIDTRANS_SERVER_KEY=Mid-server-G_MncuZhAiv9L4WpvmZ5jjGL
MIDTRANS_CLIENT_KEY=Mid-client-bML5eC8KgU0m0b4L
GOOGLE_CLIENT_ID=615293596362-95gc7m4duel9rbujis8mk5jngjalbucf.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
```

## Security Notes

⚠️ **IMPORTANT**: 
- Keep these credentials SECURE
- Never commit to Git
- Store only in Jenkins Credentials
- Rotate credentials regularly via Azure Portal
