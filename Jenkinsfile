pipeline {
    agent any

    environment {
        ACR_SERVER = 'lovecraftedacr.azurecr.io'
        IMAGE_NAME = 'lovecrafted'
        IMAGE_TAG  = 'latest'
    }

    stages {

        stage('Checkout') {
            steps {
                git branch: 'main',
                    url: 'https://github.com/Azrabelva/webccproject.git'
            }
        }

        stage('Build Docker Image') {
            steps {
                bat '''
                docker build -t %ACR_SERVER%/%IMAGE_NAME%:%IMAGE_TAG% .
                '''
            }
        }

        stage('Login to ACR') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'acr-creds',
                    usernameVariable: 'ACR_USER',
                    passwordVariable: 'ACR_PASS'
                )]) {
                    bat '''
                    docker login %ACR_SERVER% -u %ACR_USER% -p %ACR_PASS%
                    '''
                }
            }
        }

        stage('Push Image to ACR') {
            steps {
                bat '''
                docker push %ACR_SERVER%/%IMAGE_NAME%:%IMAGE_TAG%
                '''
            }
        }

        stage('Cleanup') {
            steps {
                bat 'docker image prune -f'
            }
        }
    }
}
