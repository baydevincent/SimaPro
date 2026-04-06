FROM nginx:alpine

# Copy nginx configuration
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Set working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
