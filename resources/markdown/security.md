---
title: Security
order: 3
---

# Security at Flow Forms

Security isn't just a feature—it's the foundation of everything we build. We follow industry-standard security frameworks and leverage enterprise-grade infrastructure to protect your data.

## Security Framework

At Flow Forms, we align our security practices with the [CIS Controls](https://www.cisecurity.org/controls), a leading set of security best practices. These controls guide our approach to risk reduction, helping us proactively defend against evolving cyber threats and ensure your data remains protected at every layer.
## Infrastructure

### Amazon Web Services
Our infrastructure runs on AWS, certified for **SOC 1/2/3**, **ISO 27001**, and **PCI DSS Level 1** compliance.
- Multi-factor authentication required for all production access
- Access logging and monitoring on all systems
- Principle of least privilege for all permissions

### Neon Postgres
Our database infrastructure is **SOC 2 Type 2** compliant with:
- Automatic encryption at rest (AES-256)
- Encryption in transit (TLS 1.2+)
- Automated backups with point-in-time recovery
- Network isolation and private endpoints

### Cloudflare
All traffic is protected by Cloudflare's **SOC 2 Type 2** and **ISO 27001** certified network:
- Enterprise DDoS protection
- Web application firewall (WAF)
- Bot protection and rate limiting

## Data Protection

- **All data encrypted** at rest using strong encryption
- **All connections secured** with encryption.
- **Passwords hashed** using salted hashes.

## Application Security

- Multi-factor authentication available
- Role-based access control
- CSRF protection on all forms
- XSS prevention through output encoding
- Secure headers (HSTS, CSP, X-Frame-Options)
- Automated vulnerability scanning
- Required and automated code reviews before deployment

## Monitoring & Response

- 24/7 automated security monitoring
- Real-time alerting for anomalies
- Automated vulnerability scanning
- Regular backup testing

## Incident Response

If a security incident occurs:
1. Immediate containment and resolution
2. Clear communication via [status.flowforms.io](https://status.flowforms.io)
3. Post-incident analysis and improvements

## Security Contact

Questions or concerns? Contact us directly:

[**Contact our security team**](https://www.flowforms.io/contact)
