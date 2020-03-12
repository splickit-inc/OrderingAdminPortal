node('jenkins-slave') {
    def container_image
    def namespace = "bluegreen"
    def app_name = "adminportal"
    def ecr_repo_name = "portal"
    def ecr_account = "619629683428.dkr.ecr.us-east-1.amazonaws.com"
    def ecr_repo_uri = "${ecr_account}/${ecr_repo_name}"
    def deployment = "${app_name}-deployment"


    def scmVars = checkout scm

    def image_tag = "${BRANCH_NAME}-jenkins-build-${env.BUILD_NUMBER}-${scmVars.GIT_COMMIT}"
    def image_name = "${ecr_repo_uri}:${image_tag}"    
    def live_service = "${app_name}-live-service"
    def live_color = ""
    def stage_color = ""    

       stage('Build Static Assets') {
        nodejs(nodeJSInstallationName: 'Node8-10-0') {
            sh "cd public && npm install -g grunt-cli && npm install && bower install --allow-root --config.interactive=false && npm install grunt-wiredep wiredep grunt load-grunt-tasks generator-cg-angular grunt-angular-templates grunt-browser-output grunt-contrib-clean grunt-contrib-concat grunt-contrib-connect grunt-contrib-copy grunt-contrib-cssmin grunt-contrib-htmlmin grunt-contrib-jshint grunt-contrib-less grunt-contrib-uglify grunt-contrib-watch grunt-dom-munger grunt-ng-annotate grunt-angular-templates grunt-contrib-watch && grunt wiredep"
            sh "cd public && grunt build"
        }              
    }

    stage('Build Container Image') {
        container_image = docker.build("${ecr_repo_name}")
    }    

    stage('Push Image to Repo') {
        sh "\$(aws ecr get-login --no-include-email --region us-east-1)"
        docker.withRegistry("https://${ecr_repo_uri}") {
            container_image.push("${image_tag}")
        }
    }

    if (BRANCH_NAME == 'master') {
        withEnv(["KUBECONFIG=$HOME/.kube/config-cluster","PATH=$PATH:$HOME/bin"]) {
            live_color = sh(returnStdout: true, script: "kubectl -n ${namespace} get svc ${live_service} -o jsonpath='{.spec.selector.environment}'")
            if ( live_color == "blue" ) stage_color = "green"
            else if ( live_color == "green" ) stage_color = "blue"
            else throw new Exception("Cannot identify blue/green state")    

            stage("Deploy Release to Standby Environment") {
                sh "kubectl -n ${namespace} set image deployment ${deployment}-${stage_color} ${app_name}-container=${image_name}"
                def status = sh(returnStatus: true, script: "kubectl -n ${namespace} rollout status deployment ${deployment}-${stage_color} -w")
                if (status != 0) {
                    sh "kubectl -n ${namespace} rollout undo deployment ${deployment}-${stage_color}"
                    currentBuild.result = 'FAILED'
                    error "Deployment to ${stage_color} Environment Failed"
                }          
            }

            stage('Promote to Production?') {
                try {
                     timeout(time: 60, unit: 'MINUTES') {
                         def user_input = input message: 'Promote to Production?', ok: 'Promote'
                         sh "kubectl -n ${namespace} get svc ${live_service} -o yaml | kubectl set selector --local -f - environment=${stage_color},app=${app_name} -o yaml | kubectl -n ${namespace} apply -f -"
                    }
                } catch(err)  { // timeout reached or input false
                    echo "Aborted"
                }
            }
        }
    }
}
