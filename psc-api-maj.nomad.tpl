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
            canary            = 1
            min_healthy_time  = "30s"
            progress_deadline = "5m"
            healthy_deadline  = "2m"
            auto_revert       = true
            auto_promote      = true
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
                    APP_URL=http://{{ range service "psc-api-maj" }}{{ .Address }}:{{ .Port }}{{ end }}/api
                    LOG_CHANNEL=errorlog
                    LOG_LEVEL=info
                    MONGO_DB_DATABASE=mongodb
                    MONGO_DB_HOST = {{ range service "psc-mongodb" }}{{ .Address }}{{ end }}
                    MONGO_DB_PORT = {{ range service "psc-mongodb" }}{{ .Port }}{{ end }}
                    MONGO_DB_USERNAME = {{ with secret "psc-ecosystem/mongodb" }}{{ .Data.data.root_user }}{{ end }}
                    MONGO_DB_PASSWORD = {{ with secret "psc-ecosystem/mongodb" }}{{ .Data.data.root_pass }}{{ end }}
                    QUEUE_CONNECTION=database
                EOH
                destination = "secrets/.env"
                change_mode = "noop"  // this is a problem
                env = true
            }
            resources {
                cpu = 2048
                memory = 512
            }
            service {
                name = "$\u007BNOMAD_JOB_NAME\u007D"
                canary_tags = ["canary instance to promote"]
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

