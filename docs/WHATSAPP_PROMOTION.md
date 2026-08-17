# WhatsApp promotion guide — MediQueue OPD

Use these templates for **manual, opt-in outreach** (WhatsApp Business broadcast lists or one-to-one messages). Do not send unsolicited bulk messages — it violates WhatsApp policy and Indian DND rules.

## Before you share

1. Set your public URL in `.env`:
   ```env
   APP_URL=https://your-domain.com
   ```
2. Share the promo page: **`/promo`** (includes a “Share on WhatsApp” button)
3. Only message contacts who **agreed** to receive updates

---

## Message — personal (no links) — from Shashank

Use `/promo?to=TheirName` to personalize the greeting.

```
Hi [Name],

I'm Shashank. We built MediQueue OPD — Smart Hospital Queue Management.

What it does:
✅ Online OPD booking (76+ cities in demo)
✅ Token + QR for every patient
✅ Live queue position & ETA on phone
✅ Admin desk for hospitals & clinics

I'd love to show you a quick 10-minute demo.

Reply on WhatsApp or email me at sunnyns60@gmail.com

Thanks,
Shashank
```

Configure in `.env`:
```env
OUTREACH_CONTACT_NAME=Shashank
OUTREACH_CONTACT_EMAIL=sunnyns60@gmail.com
```

---

## Message 1 — Hospital / clinic owner (B2B) — with links (optional)

```
🏥 MediQueue OPD — Smart Hospital Queue Management

Hi [Name], we built a live demo for outpatient queues:

✅ Patients book online (city → doctor → slot)
✅ Auto token + QR at confirmation
✅ Live queue position & ETA on phone
✅ Admin desk: call next, serve, complete
✅ Offline / online / advance payment modes

*Live demo:* [YOUR_URL]/book
*Admin console:* [YOUR_URL]/admin/login
Login: admin@mediqueue.local / password

76+ cities & 150+ hospitals already seeded for testing.

Can I show you a 10-min walkthrough this week?
```

---

## Message 2 — Short follow-up (after no reply)

```
Hi [Name], quick follow-up on MediQueue OPD demo 👋

One link to try on your phone:
[YOUR_URL]/promo

Book a test appointment in ~2 minutes — no signup needed for patients.

Happy to customize for your hospital name & doctors if interested.
```

---

## Message 3 — Patient / general audience (B2C)

```
Skip the waiting room guesswork 🏥

MediQueue OPD lets you:
• Book a doctor slot online
• Get your token + QR code
• See live queue position & ETA

Try the demo: [YOUR_URL]/book

Works across 76+ Indian cities in our demo data.
```

---

## Message 4 — WhatsApp Status / bio

```
🏥 MediQueue OPD — book OPD + live queue tracking
Demo: [YOUR_URL]/promo
```

---

## WhatsApp Business broadcast (recommended)

1. Install **WhatsApp Business**
2. Save contacts who **opted in** (they must have your number saved)
3. Menu → **New broadcast** → paste **Message 1**
4. Replace `[YOUR_URL]` with your real domain
5. Send to opted-in list only

---

## In-app share page

Open **`/promo`** on your deployed site — it includes:

- Pre-filled WhatsApp share link (`wa.me/?text=...`)
- Copy-paste message box
- Direct links to book & admin demo

---

## Quick public URL (development)

To share localhost temporarily:

```bash
# Install ngrok, then:
ngrok http 8000
```

Set `APP_URL` to the ngrok HTTPS URL, then share `[ngrok-url]/promo`.

See [DEPLOY.md](DEPLOY.md) for production hosting.

---

## Compliance checklist

- [ ] Recipient opted in or existing business relationship
- [ ] Public HTTPS URL (not raw IP for trust)
- [ ] Include opt-out: “Reply STOP if not interested”
- [ ] Avoid messaging DND-registered numbers for promotional content
- [ ] Use official WhatsApp Business API for automated notifications (Module 5)
