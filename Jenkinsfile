pipeline {
    agent any

    stages {

        stage('Checkout') {
            steps {
                git branch: 'main',
                    credentialsId: 'github-credentials',
                    url: 'https://github.com/Azrabelva/webccproject.git'
            }
        }

        stage('Docker Build') {
            steps {
                sh 'docker build -t lovecrafted-app .'
            }
        }

        stage('Create Volume (if not exists)') {
            steps {
                sh '''
                docker volume inspect lovecrafted-data > /dev/null 2>&1 || \
                docker volume create lovecrafted-data
                '''
            }
        }

        stage('Deploy with Volume') {
            steps {
                sh '''
                docker stop lovecrafted || true
                docker rm lovecrafted || true

                docker run -d \
                  -p 8081:80 \
                  -v lovecrafted-data:/var/www/html/uploads \
                  --name lovecrafted \
                  lovecrafted-app
                '''
            }
        }
    }
}
