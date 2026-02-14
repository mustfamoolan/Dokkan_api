#!/bin/bash

# Configuration
DOMAIN="salesflowi.com"
EMAIL="admin@salesflowi.com" # You can change this
APP_PORT="8000"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}🚀 Starting Domain Setup for $DOMAIN...${NC}"

# 1. Install Nginx & Certbot
echo -e "${YELLOW}📦 Installing Nginx and Certbot...${NC}"
sudo apt update
sudo apt install -y nginx certbot python3-certbot-nginx

# 2. Configure Nginx
echo -e "${YELLOW}abb Configuring Nginx...${NC}"
CONFIG_FILE="/etc/nginx/sites-available/$DOMAIN"

sudo tee $CONFIG_FILE > /dev/null <<EOF
server {
    server_name $DOMAIN www.$DOMAIN;

    location / {
        proxy_pass http://127.0.0.1:$APP_PORT;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
    }
}
EOF

# 3. Enable Site
echo -e "${YELLOW}🔗 Enabling site configuration...${NC}"
sudo ln -sf $CONFIG_FILE /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# 4. Test and Restart Nginx
echo -e "${YELLOW}🔄 Restarting Nginx...${NC}"
sudo nginx -t && sudo systemctl restart nginx

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Nginx configured successfully!${NC}"
else
    echo -e "${RED}❌ Nginx configuration failed. Please check logs.${NC}"
    exit 1
fi

# 5. Obtain SSL Certificate
echo -e "${YELLOW}🔒 Obtaining SSL Certificate (Let's Encrypt)...${NC}"
echo -e "${YELLOW}⚠️  Note: You might be asked to enter an email address.${NC}"

sudo certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos -m $EMAIL --redirect

if [ $? -eq 0 ]; then
    echo -e "${GREEN}🎉 SSL Certificate installed successfully!${NC}"
    echo -e "${GREEN}🌐 Your site is now live at: https://$DOMAIN${NC}"
else
    echo -e "${RED}❌ SSL installation failed. Ensure your DNS records point to this server IP.${NC}"
fi
