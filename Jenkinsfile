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

        stage('Build') {
            steps {
                sh 'docker build -t lovecrafted-app .'
            }
        }

        stage('Deploy') {
            steps {
                sh '''
                docker stop lovecrafted || true
                docker rm lovecrafted || true
                docker run -d -p 8081:80 --name lovecrafted lovecrafted-app
                '''
            }
        }
    }
}
