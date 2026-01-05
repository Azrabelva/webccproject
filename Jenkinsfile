
pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                echo 'Checkout source code'
                sh 'ls'
            }
        }

        stage('Build') {
            steps {
                echo 'Build process'
                sh 'echo Build sukses'
            }
        }

        stage('Test') {
            steps {
                echo 'Testing'
                sh 'echo Test sukses'
            }
        }

        stage('Deploy') {
            steps {
                echo 'Deploy'
                sh 'echo Deploy selesai'
            }
        }
    }
}
