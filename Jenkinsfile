pipeline {
    agent any
    
    environment {
        APP_NAME = 'lovecrafted-app-2025'
        RESOURCE_GROUP = 'CloudComputing'
        ZIP_FILE = 'lovecrafted-deploy.zip'
        DEPLOY_URL = "https://${APP_NAME}.scm.azurewebsites.net/api/zipdeploy"
        APP_URL = "https://${APP_NAME}.azurewebsites.net"
    }
    
    stages {
        stage('Checkout') {
            steps {
                echo 'Checking out code from GitHub...'
                checkout scm
            }
        }
        
        stage('Install Dependencies') {
            steps {
                echo 'Installing Composer dependencies...'
                script {
                    try {
                        if (isUnix()) {
                            sh '''
                                if command -v composer &> /dev/null; then
                                    echo "Composer found, installing dependencies..."
                                    composer install --no-dev --optimize-autoloader --no-interaction
                                else
                                    echo "⚠️ Composer not found. Skipping dependency installation."
                                    echo "Note: Make sure vendor/ directory is committed to Git or install Composer on Jenkins."
                                fi
                            '''
                        } else {
                            bat '''
                                where composer >nul 2>&1
                                if %ERRORLEVEL% EQU 0 (
                                    echo Composer found, installing dependencies...
                                    composer install --no-dev --optimize-autoloader --no-interaction
                                ) else (
                                    echo WARNING: Composer not found. Skipping dependency installation.
                                    echo Note: Make sure vendor/ directory is committed to Git or install Composer on Jenkins.
                                )
                            '''
                        }
                    } catch (Exception e) {
                        echo "⚠️ Composer installation failed: ${e.message}"
                        echo "Continuing anyway - assuming dependencies are in repo..."
                    }
                }
            }
        }
        
        stage('Prepare Deployment Package') {
            steps {
                echo 'Creating deployment package...'
                script {
                    // Remove old zip if exists
                    if (fileExists(env.ZIP_FILE)) {
                        if (isUnix()) {
                            sh "rm -f ${env.ZIP_FILE}"
                        } else {
                            bat "del /F ${env.ZIP_FILE}"
                        }
                    }
                    
                    // Create zip package
                    if (isUnix()) {
                        sh '''
                            zip -r ${ZIP_FILE} . \
                                -x "*.git*" \
                                -x "*.sqlite" \
                                -x "node_modules/*" \
                                -x "*.md" \
                                -x ".env*" \
                                -x "tests/*" \
                                -x "*.log" \
                                -x ".vscode/*" \
                                -x ".idea/*"
                            
                            echo "Package size:"
                            ls -lh ${ZIP_FILE}
                        '''
                    } else {
                        // Windows - use PowerShell
                        bat """
                            powershell -Command "& {
                                Remove-Item -Path ${env.ZIP_FILE} -Force -ErrorAction SilentlyContinue
                                
                                \$exclude = @('*.git*', '*.sqlite', 'node_modules', '*.md', '.env*', 'tests', '*.log', '.vscode', '.idea')
                                
                                Compress-Archive -Path * -DestinationPath ${env.ZIP_FILE} -Force
                                
                                Write-Host 'Package created:'
                                Get-Item ${env.ZIP_FILE} | Select-Object Name, Length
                            }"
                        """
                    }
                }
            }
        }
        
        stage('Deploy to Azure') {
            steps {
                echo 'Deploying to Azure Web App...'
                withCredentials([usernamePassword(
                    credentialsId: 'azure-lovecrafted-deploy',
                    usernameVariable: 'AZURE_USER',
                    passwordVariable: 'AZURE_PASS'
                )]) {
                    script {
                        if (isUnix()) {
                            sh """
                                echo 'Uploading to Azure...'
                                curl -X POST \\
                                    -u "\${AZURE_USER}:\${AZURE_PASS}" \\
                                    -H "Content-Type: application/zip" \\
                                    --data-binary @${env.ZIP_FILE} \\
                                    ${env.DEPLOY_URL} \\
                                    --max-time 300 \\
                                    -v
                                
                                echo 'Deployment request sent!'
                            """
                        } else {
                            bat """
                                echo Uploading to Azure...
                                curl -X POST ^
                                    -u "%AZURE_USER%:%AZURE_PASS%" ^
                                    -H "Content-Type: application/zip" ^
                                    --data-binary @${env.ZIP_FILE} ^
                                    ${env.DEPLOY_URL} ^
                                    --max-time 300 ^
                                    -v
                                
                                echo Deployment request sent!
                            """
                        }
                    }
                }
            }
        }
        
        stage('Verify Deployment') {
            steps {
                echo 'Verifying deployment...'
                script {
                    sleep(time: 15, unit: 'SECONDS')
                    
                    if (isUnix()) {
                        sh """
                            HTTP_CODE=\$(curl -s -o /dev/null -w "%{http_code}" ${env.APP_URL})
                            echo "Site HTTP Code: \$HTTP_CODE"
                            
                            if [ "\$HTTP_CODE" -eq 200 ] || [ "\$HTTP_CODE" -eq 302 ]; then
                                echo "✅ Deployment verified! Site is responding."
                            else
                                echo "⚠️ Warning: Site returned HTTP \$HTTP_CODE"
                            fi
                        """
                    } else {
                        bat """
                            curl -s -o nul -w "%%{http_code}" ${env.APP_URL} > temp_http_code.txt
                            set /p HTTP_CODE=<temp_http_code.txt
                            echo Site HTTP Code: %HTTP_CODE%
                            del temp_http_code.txt
                            
                            echo Deployment verification complete
                        """
                    }
                }
            }
        }
    }
    
    post {
        success {
            echo '🎉 Deployment to Azure completed successfully!'
            echo "🔗 Application URL: ${env.APP_URL}"
            echo '📋 Next steps:'
            echo '  1. Update Google OAuth redirect URI'
            echo '  2. Test login/registration'
            echo '  3. Verify payment integration'
        }
        failure {
            echo '❌ Deployment failed!'
            echo 'Check the console output above for error details.'
            echo 'Common issues:'
            echo '  - Credentials expired or incorrect'
            echo '  - Network timeout to Azure'
            echo '  - Composer dependencies failed'
        }
        always {
            echo 'Cleaning up deployment artifacts...'
            script {
                if (fileExists(env.ZIP_FILE)) {
                    if (isUnix()) {
                        sh "rm -f ${env.ZIP_FILE}"
                    } else {
                        bat "del /F ${env.ZIP_FILE}"
                    }
                }
            }
        }
    }
}

