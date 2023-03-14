@Library("kununu@php-microservice-on-80") _

withEnv([
    "SERVICE_NAME=trace-id-provider",
    "LOG_JUNIT_EXPORT=tests/.results/tests-junit.xml",
    "LOG_CLOVER_EXPORT=tests/.results/tests-clover.xml"
    ]) {
    ansiColor {
        timestamps {
            defaultPipeline.getSource()
            defaultPipeline.runPhpLibTests()
            defaultPipeline.runSonar("php")

            if (env.BRANCH_NAME in ["master"]) {
                defaultPipeline.publishPhpPackage()
            }
        }
    }
}
