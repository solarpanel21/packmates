# Use nginx to serve the static website
FROM nginx:alpine AS final

# Create a non-privileged user for security
ARG UID=10001
RUN adduser \
    --disabled-password \
    --gecos "" \
    --home "/nonexistent" \
    --shell "/sbin/nologin" \
    --no-create-home \
    --uid "${UID}" \
    appuser

# Copy all website files into nginx's default serving directory
COPY . /usr/share/nginx/html/

# Expose port 80 for HTTP traffic
EXPOSE 80

# Start nginx in the foreground
ENTRYPOINT ["nginx", "-g", "daemon off;"]
