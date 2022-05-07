
# TYPO3: Block Malicious Login Attempts
 - When a backend login fails multiple times, the IP gets blacklisted, so that no further login attempts will be possible.
 - Reset blocked IP addresses in the backend module.
 - Display a custom error message to users whose IP address has been blocked.
 - Set a time limit for blocked IPs to be eligible to login again.
 - Block failed login attempts by username, additionally to IP blocking.
 