---
title: Trusted Devices (Account-Wide)
order: 3
---

# Trusted Devices (Account-Wide)

Trusted Devices allow administrators to register company-owned devices (like shared iPads or kiosk computers) so that **any user** logging in on those devices can skip the device verification step.

> **Note:** This is different from [per-user device verification](/help/getting-started/device-trust-verification), where each user's devices are tracked individually. Account-wide trusted devices apply to all users in your organization.

## Overview

When device verification is enabled for your account, users are typically required to verify their identity via email each time they log in from a new device. While this adds security, it can create friction for shared devices where users may not have immediate access to their email.

Trusted Devices solve this problem by allowing administrators to mark specific devices as "trusted" at the account level. Any user logging in from a trusted device will automatically bypass device verification.

## When to Use Trusted Devices

Trusted Devices are ideal for:

- **Front desk iPads** - Reception areas where multiple staff members need quick access
- **Kiosk computers** - Shared workstations in common areas
- **Conference room devices** - Tablets or computers used for presentations
- **Shared workstations** - Any company-owned device used by multiple employees

## Managing Trusted Devices

### Accessing the Trusted Devices Manager

1. Navigate to **Account Settings**
2. In the **Advanced** section, click **Manage Trusted Devices**

### Registering a Device as Trusted

To register the device you're currently using:

1. Open the Trusted Devices manager
2. Click **Register This Device**
3. Enter a descriptive name for the device (e.g., "Front Desk iPad", "Conference Room A Tablet")
4. Click **Register Device**

The device will now appear in your list of trusted devices with a "This Device" badge.

### Registering During Login

If you're an administrator and encounter the device verification screen on a new device, you can register it as trusted directly from that screen:

1. On the verification code screen, look for the **Register as Trusted Device** option
2. Click the link to register the device
3. Enter a descriptive name
4. Complete registration

This allows you to set up trusted devices without needing to go through the verification process first.

### Removing a Trusted Device

To remove a device from the trusted list:

1. Open the Trusted Devices manager
2. Find the device you want to remove
3. Click the trash icon next to the device
4. Confirm the removal

After removal, users will need to verify their device the next time they log in from that device.

## Understanding the Device List

Each trusted device shows:

- **Device name** - The name you assigned when registering
- **Registered by** - The administrator who registered the device
- **Last used** - When the device was last used for login
- **Last user** - Who last logged in from this device (if not currently active)
- **This Device badge** - Indicates you're currently using this device
- **Active now** - Shows when you're viewing from this device

## Security Considerations

### When Trusted Devices Are Appropriate

Trusted Devices are designed for company-owned, physically secured devices. Consider using them when:

- The device is owned by your organization
- The device is in a controlled environment
- Multiple authorized users need quick access
- Users may not have immediate access to verification emails

### When to Avoid Trusted Devices

Do not register devices as trusted when:

- The device is personally owned
- The device leaves your physical premises
- The device is accessible to unauthorized individuals
- You need to track individual device verification per user

### Best Practices

1. **Use descriptive names** - Name devices by location or purpose for easy identification
2. **Audit regularly** - Review your trusted devices list periodically and remove any that are no longer in use
3. **Limit registrations** - Only register devices that genuinely need to bypass verification
4. **Physical security** - Ensure trusted devices are in secure, supervised locations
