pipeline {
    agent any
    
    environment {
        APP_NAME = 'lovecrafted-app-2025'
        RESOURCE_GROUP = 'CloudComputing'
        ZIP_FILE = 'lovecrafted-deploy.zip'
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
                sh '''
                    composer install --no-dev --optimize-autoloader
                '''
            }
        }
        
        stage('Prepare Deployment Package') {
            steps {
                echo 'Creating deployment package...'
                sh '''
                    # Remove existing zip if exists
                    rm -f ${ZIP_FILE}
                    
                    # Create zip excluding unnecessary files
                    zip -r ${ZIP_FILE} . \
                        -x "*.git*" \
                        -x "*.sqlite" \
                        -x "node_modules/*" \
                        -x "*.md" \
                        -x ".env" \
                        -x "tests/*" \
                        -x "*.log"
                    
                    echo "Deployment package created: ${ZIP_FILE}"
                    ls -lh ${ZIP_FILE}
                '''
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
                    sh '''
                        # Deploy using Kudu ZIP Deploy API
                        curl -X POST \
                            -u "${AZURE_USER}:${AZURE_PASS}" \
                            -H "Content-Type: application/zip" \
                            --data-binary @${ZIP_FILE} \
                            https://${APP_NAME}.scm.azurewebsites.net/api/zipdeploy
                        
                        echo "Deployment completed!"
                    '''
                }
            }
        }
        
        stage('Verify Deployment') {
            steps {
                echo 'Verifying deployment...'
                sh '''
                    # Wait for app to restart
                    sleep 10
                    
                    # Check if site is responding
                    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" \
                        https://${APP_NAME}.azurewebsites.net)
                    
                    if [ "$HTTP_CODE" -eq 200 ] || [ "$HTTP_CODE" -eq 302 ]; then
                        echo "✅ Deployment successful! Site is responding (HTTP $HTTP_CODE)"
                    else
                        echo "⚠️ Warning: Site returned HTTP $HTTP_CODE"
                    fi
                '''
            }
        }
    }
    
    post {
        success {
            echo '🎉 Deployment to Azure completed successfully!'
            echo "🔗 URL: https://${APP_NAME}.azurewebsites.net"
        }
        failure {
            echo '❌ Deployment failed. Check logs for details.'
        }
        always {
            echo 'Cleaning up...'
            sh 'rm -f ${ZIP_FILE}'
        }
    }
}
