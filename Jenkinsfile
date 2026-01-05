pipeline {
    agent any

    environment {
        DOCKERHUB_USER = 'pashaputri'
        IMAGE_NAME     = 'lovecrafted'
        IMAGE_TAG      = 'latest'
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
                docker build -t %DOCKERHUB_USER%/%IMAGE_NAME%:%IMAGE_TAG% .
                '''
            }
        }

        stage('Push to Docker Hub') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'dockerhub-creds',
                    usernameVariable: 'DOCKER_USER',
                    passwordVariable: 'DOCKER_PASS'
                )]) {
                    bat '''
                    echo %DOCKER_PASS% | docker login -u %DOCKER_USER% --password-stdin
                    docker push %DOCKERHUB_USER%/%IMAGE_NAME%:%IMAGE_TAG%
                    docker logout
                    '''
                }
            }
        }

        stage('Cleanup') {
            steps {
                bat 'docker image prune -f'
            }
        }
    }
}
