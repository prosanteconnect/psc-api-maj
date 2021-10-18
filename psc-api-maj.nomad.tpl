job "psc-api-maj" {
    datacenters = ["${datacenter}"]
    type = "service"

    vault {
        policies = ["psc-ecosystem"]
        change_mode = "restart"
    }
    group "psc-api-maj" {
        count = "1"
        restart {
            attempts = 3
            delay = "60s"
            interval = "1h"
            mode = "fail"
        }
        update {
            max_parallel      = 1
            min_healthy_time  = "30s"
            progress_deadline = "5m"
            healthy_deadline  = "2m"
        }
        network {
            mode = "host"
            port "http" {
                to = 80
            }
        }
        task "psc-api-maj" {
            driver = "docker"
            config {
                image = "${artifact.image}:${artifact.tag}"
                ports = ["http"]
            }
            template {
                data = <<EOH
                    APP_NAME=psc-api-maj
                    APP_ENV=production
                    APP_KEY={{ with secret "psc-ecosystem/psc-api-maj" }}{{ .Data.data.app_key }}{{ end }}
                    APP_DEBUG=false
                    LOG_CHANNEL=errorlog
                    LOG_LEVEL=info
                    MONGO_DB_DATABASE=mongodb
                    {{ range service "psc-mongodb" }}MONGO_DB_HOST = {{ .Address }}
                    MONGO_DB_PORT = {{ .Port }}{{ end }}
                    MONGO_DB_USERNAME = {{ with secret "psc-ecosystem/mongodb" }}{{ .Data.data.root_user }}{{ end }}
                    MONGO_DB_PASSWORD = {{ with secret "psc-ecosystem/mongodb" }}{{ .Data.data.root_pass }}{{ end }}
                    QUEUE_CONNECTION=database
                EOH
                destination = "secrets/.env"
                change_mode = "restart"
                env = true
            }
            resources {
                cpu = 2048
                memory = 512
            }
            service {
                name = "$\u007BNOMAD_JOB_NAME\u007D"
                port = "http"
                check {
                    type = "tcp"
                    port = "http"
                    interval = "10s"
                    timeout = "2s"
                }
            }
        }
    }
}

