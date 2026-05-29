# VaultOS — Self-Storage Management App

Laravel 11 + Vue 3 + MSSQL, containerised with Docker and deployed on AWS ECS Fargate.

---

## Prerequisites

- Docker and Docker Compose
- AWS CLI configured with an IAM user that has ECR and ECS permissions
- GitHub repository secrets (see below)

---

## Local Development

1. Copy `.env.example` to `.env` and set `APP_KEY` (or run `php artisan key:generate` after the container is up).

2. Start the stack:

   ```bash
   docker-compose up --build
   ```

3. Run migrations and seeders:

   ```bash
   docker-compose exec app php artisan migrate --seed
   ```

4. Open http://localhost:8080 in your browser.

---

## AWS Setup (one-time)

### 1. Create the ECR repository

```bash
aws ecr create-repository \
  --repository-name vaultos-app \
  --region ap-southeast-2
```

### 2. Store secrets in SSM Parameter Store

```bash
aws ssm put-parameter --name /vaultos/app-key     --type SecureString --value "base64:YOUR_APP_KEY"
aws ssm put-parameter --name /vaultos/db-password --type SecureString --value "VaultOS_Str0ng!"
```

### 3. Create the ECS cluster

```bash
aws ecs create-cluster --cluster-name vaultos-cluster --region ap-southeast-2
```

### 4. Register the task definition

Replace `ACCOUNT_ID` in `ecs-task-definition.json` with your AWS account ID, then:

```bash
aws ecs register-task-definition \
  --cli-input-json file://ecs-task-definition.json \
  --region ap-southeast-2
```

### 5. Create the ECS service

Create a Fargate service attached to an Application Load Balancer (ALB) that forwards traffic on port 80 to the `app` container. Use the AWS Console or CLI.

### 6. Attach an ALB and configure a security group

The ALB listener should forward HTTP:80 to the ECS target group. The ECS task security group must allow inbound traffic from the ALB security group on port 80.

---

## GitHub Actions Secrets Required

| Secret                  | Description                              |
|-------------------------|------------------------------------------|
| `AWS_ACCESS_KEY_ID`     | IAM access key with ECR + ECS permissions |
| `AWS_SECRET_ACCESS_KEY` | Corresponding IAM secret key             |

Pushes to `master` automatically build the Docker image, push it to ECR, and deploy a rolling update to the ECS service.

---

## Deployed URL

https://YOUR-ALB-DNS-NAME.ap-southeast-2.elb.amazonaws.com
