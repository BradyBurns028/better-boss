FROM nginx:alpine

# Create web root directory
RUN mkdir -p /var/www/backend

# Copy only the public directory (important for security)
COPY ./public /var/www/backend/public

# Copy custom NGINX configuration
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Set working directory
WORKDIR /etc/nginx

# Expose port 80
EXPOSE 80

# Start NGINX
CMD ["nginx", "-g", "daemon off;"]
