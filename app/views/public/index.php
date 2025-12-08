<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Play & Win Big' ?> | eTickets Raffle</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #0f0f23;
            color: #fff;
            overflow-x: hidden;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            opacity: 0.1;
            z-index: -1;
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background-image: 
                radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px),
                radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            background-position: 0 0, 25px 25px;
            animation: moveBackground 20s linear infinite;
        }

        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(15, 15, 35, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 2px solid rgba(102, 126, 234, 0.3);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: #f093fb !important;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero-section {
            padding: 120px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .hero-title {
            font-size: 4.5rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #fff, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 80px rgba(240, 147, 251, 0.5);
        }

        .hero-subtitle {
            font-size: 1.4rem;
            color: rgba(255,255,255,0.8);
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
            padding: 1.2rem 3rem;
            font-size: 1.2rem;
            font-weight: 700;
            border-radius: 50px;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.6);
            color: white;
        }

        .btn-secondary-custom {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 1.2rem 3rem;
            font-size: 1.2rem;
            font-weight: 700;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .btn-secondary-custom:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-3px);
            color: white;
        }

        /* Lottery Balls Animation */
        .lottery-visual {
            position: relative;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Prize Wheel */
        .lottery-ball-machine {
            position: relative;
            width: 400px;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        /* Pointer at top */
        .wheel-pointer {
            position: absolute;
            top: -18px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-bottom: 35px solid #ffd32a;
            filter: drop-shadow(0 0 8px rgba(0, 0, 0, 0.5));
            z-index: 5;
        }

        .wheel-pointer::after {
            content: '';
            position: absolute;
            top: 28px;
            left: -12px;
            width: 24px;
            height: 10px;
            background: #222;
            border-radius: 999px;
        }

        /* Wheel container */
        .lottery-cage {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            filter: drop-shadow(0 0 20px rgba(0, 0, 0, 0.6));
        }

        /* Wheel inner with segments */
        .cage-inner {
            --num-segments: 8;
            --segment-angle: calc(360deg / var(--num-segments));
            
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 10px solid #f5f5f5;
            background: conic-gradient(
                #ff7675 0deg 45deg,
                #ffeaa7 45deg 90deg,
                #74b9ff 90deg 135deg,
                #55efc4 135deg 180deg,
                #a29bfe 180deg 225deg,
                #fd79a8 225deg 270deg,
                #fdcb6e 270deg 315deg,
                #00cec9 315deg 360deg
            );
            transition: transform 4s cubic-bezier(0.25, 0.9, 0.25, 1.1);
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* Center circle */
        .cage-center {
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #ffffff, #dfe6e9);
            border: 6px solid #2d3436;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 15;
            box-shadow:
                inset 0 0 12px rgba(0, 0, 0, 0.45),
                0 0 12px rgba(0, 0, 0, 0.5);
        }

        .cage-center span {
            font-weight: 700;
            font-size: 0.85rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #2d3436;
            line-height: 1.3;
        }

        /* Prize labels */
        .prize-label {
            position: absolute;
            left: 50%;
            top: 50%;
            transform-origin: 0 0;
            transform: rotate(calc(var(--i) * var(--segment-angle) + var(--segment-angle) / 2)) translate(0, -50%);
            width: 45%;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-right: 25px;
            z-index: 10;
            pointer-events: none;
        }

        .prize-label span {
            display: inline-block;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 5px 10px;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(3px);
            white-space: nowrap;
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            transform: rotate(180deg);
        }

        /* Spin Button */
        .spin-button {
            margin-top: 2rem;
            padding: 0.9rem 2.5rem;
            border-radius: 999px;
            border: none;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            cursor: pointer;
            background: linear-gradient(135deg, #ff9f1a, #ff3838);
            color: white;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.4);
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
        }

        .spin-button:hover:enabled {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(0, 0, 0, 0.6);
            filter: brightness(1.05);
        }

        .spin-button:active:enabled {
            transform: translateY(0);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.4);
        }

        .spin-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Result Modal */
        .result-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }

        .result-modal.show {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .result-content {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 30px;
            padding: 3rem 2.5rem;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.5s ease;
            position: relative;
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes slideUp {
            from {
                transform: translateY(100px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .result-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: bounce 1s ease infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .result-title {
            font-size: 2rem;
            font-weight: 900;
            color: white;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .result-prize {
            font-size: 3rem;
            font-weight: 900;
            color: #ffd32a;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
            animation: pulse 1.5s ease infinite;
        }

        .result-message {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .result-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .result-btn {
            padding: 1rem 2rem;
            border-radius: 999px;
            border: none;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .result-btn-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        }

        .result-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(16, 185, 129, 0.6);
        }

        .result-btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .result-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }

        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #ffd32a;
            animation: confettiFall 3s linear infinite;
        }

        @keyframes confettiFall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }

        @keyframes spinWheel {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .lottery-machine {
            width: 100%;
            height: 100%;
            position: absolute;
            background: url('https://images.pexels.com/photos/6963944/pexels-photo-6963944.jpeg?auto=compress&cs=tinysrgb&w=800') center/contain no-repeat;
            filter: brightness(1.3) contrast(1.2) saturate(1.1);
            opacity: 0.3;
            z-index: 0;
        }


        /* Stats Bar */
        .stats-bar {
            background: rgba(15, 15, 35, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(102, 126, 234, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin: -50px auto 80px;
            position: relative;
            z-index: 10;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .stats-bar::before {
            content: '💰';
            position: absolute;
            font-size: 10rem;
            opacity: 0.05;
            top: 50%;
            right: 5%;
            transform: translateY(-50%) rotate(-15deg);
        }

        .stats-bar::after {
            content: '🏆';
            position: absolute;
            font-size: 10rem;
            opacity: 0.05;
            top: 50%;
            left: 5%;
            transform: translateY(-50%) rotate(15deg);
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: block;
        }

        .stat-label {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 0.5rem;
        }

        /* Section Titles */
        .section-title {
            font-size: 3rem;
            font-weight: 900;
            text-align: center;
            margin-bottom: 3rem;
            background: linear-gradient(135deg, #fff, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Campaign Cards */
        .campaign-card {
            background: rgba(15, 15, 35, 0.6);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 25px;
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
            position: relative;
        }

        .campaign-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }

        .campaign-card:hover {
            transform: translateY(-10px);
            border-color: rgba(240, 147, 251, 0.6);
            box-shadow: 0 20px 60px rgba(240, 147, 251, 0.3);
        }

        .campaign-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .campaign-header::before {
            content: '🎟️';
            position: absolute;
            font-size: 8rem;
            opacity: 0.1;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
        }

        .campaign-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .campaign-name {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .campaign-price {
            font-size: 3rem;
            font-weight: 900;
            margin: 1rem 0;
            text-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .campaign-body {
            padding: 2rem;
        }

        .campaign-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .info-box {
            background: rgba(102, 126, 234, 0.1);
            padding: 1rem;
            border-radius: 15px;
            text-align: center;
        }

        .info-label {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #f093fb;
        }

        .btn-campaign {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 15px;
            width: 100%;
            transition: all 0.3s;
            text-transform: uppercase;
        }

        .btn-campaign:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
            color: white;
        }

        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: rgba(15, 15, 35, 0.5);
            position: relative;
            overflow: hidden;
        }

        .features-section::before {
            content: '';
            position: absolute;
            top: 10%;
            left: -5%;
            width: 200px;
            height: 130px;
            background: url('https://images.pexels.com/photos/6963801/pexels-photo-6963801.jpeg?auto=compress&cs=tinysrgb&w=400') center/cover;
            border-radius: 15px;
            opacity: 0.12;
            transform: rotate(-20deg);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .features-section::after {
            content: '';
            position: absolute;
            bottom: 10%;
            right: -5%;
            width: 200px;
            height: 130px;
            background: url('https://images.pexels.com/photos/4968630/pexels-photo-4968630.jpeg?auto=compress&cs=tinysrgb&w=400') center/cover;
            border-radius: 15px;
            opacity: 0.12;
            transform: rotate(20deg);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .feature-card {
            background: rgba(102, 126, 234, 0.1);
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s;
            height: 100%;
        }

        .feature-card:hover {
            border-color: rgba(240, 147, 251, 0.6);
            transform: translateY(-5px);
            background: rgba(102, 126, 234, 0.2);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .feature-desc {
            color: rgba(255,255,255,0.7);
            line-height: 1.6;
        }

        /* How It Works */
        .how-it-works {
            padding: 80px 0;
            position: relative;
        }

        .how-it-works::before {
            content: '🎰';
            position: absolute;
            font-size: 15rem;
            opacity: 0.05;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        .step-card {
            text-align: center;
            padding: 2rem;
            position: relative;
        }

        .step-number {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 900;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .step-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .step-desc {
            color: rgba(255,255,255,0.7);
        }

        /* Promo Banner */
        .promo-banner {
            background: linear-gradient(135deg, #667eea, #764ba2, #f093fb);
            padding: 60px 0;
            margin: 80px 0;
            position: relative;
            overflow: hidden;
        }

        .promo-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.pexels.com/photos/6963622/pexels-photo-6963622.jpeg?auto=compress&cs=tinysrgb&w=1200') center/cover;
            opacity: 0.2;
            filter: blur(3px);
        }

        .promo-banner::after {
            content: '🎰🎲🎯🏆💰🎟️🎊✨';
            position: absolute;
            font-size: 5rem;
            opacity: 0.1;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            white-space: nowrap;
            animation: scroll 20s linear infinite;
        }

        @keyframes scroll {
            0% { transform: translate(-50%, -50%) translateX(0); }
            100% { transform: translate(-50%, -50%) translateX(-100%); }
        }

        /* Scratch Ticket Decorations */
        .scratch-ticket {
            position: absolute;
            width: 150px;
            height: 100px;
            background: url('https://images.pexels.com/photos/6963945/pexels-photo-6963945.jpeg?auto=compress&cs=tinysrgb&w=300') center/cover;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transform: rotate(-15deg);
            opacity: 0.2;
            animation: float 4s ease-in-out infinite;
        }

        .scratch-ticket:nth-child(2) {
            right: 5%;
            top: 20%;
            transform: rotate(15deg);
            animation-delay: 1s;
        }

        .promo-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .promo-title {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1rem;
        }

        .promo-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        /* Footer */
        .footer-custom {
            background: rgba(15, 15, 35, 0.95);
            border-top: 2px solid rgba(102, 126, 234, 0.3);
            padding: 60px 0 30px;
        }

        .footer-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #f093fb;
        }

        .footer-link {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }

        .footer-link:hover {
            color: #f093fb;
            padding-left: 5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                padding: 80px 0 40px;
            }

            .hero-title {
                font-size: 2.2rem;
                line-height: 1.2;
            }

            .hero-subtitle {
                font-size: 1rem;
                margin-bottom: 1.5rem;
            }

            .hero-badge {
                font-size: 0.8rem;
                padding: 0.4rem 1rem;
            }

            .hero-cta {
                flex-direction: column;
                width: 100%;
            }

            .btn-primary-custom,
            .btn-secondary-custom {
                width: 100%;
                padding: 1rem 2rem;
                font-size: 1rem;
            }

            .lottery-visual {
                height: 250px;
                margin-top: 2rem;
            }

            .lottery-machine {
                opacity: 0.4;
            }

            .lottery-ball-machine {
                width: 280px;
                height: 280px;
            }

            .cage-inner {
                border-width: 8px;
            }

            .cage-center {
                width: 70px;
                height: 70px;
                border-width: 4px;
            }

            .cage-center span {
                font-size: 0.7rem;
            }

            .wheel-pointer {
                top: -14px;
                border-left: 16px solid transparent;
                border-right: 16px solid transparent;
                border-bottom: 28px solid #ffd32a;
            }

            .wheel-pointer::after {
                width: 20px;
                height: 8px;
                top: 22px;
                left: -10px;
            }

            .prize-label {
                padding-right: 18px;
            }

            .prize-label span {
                font-size: 0.7rem;
                padding: 4px 7px;
            }

            .section-title {
                font-size: 1.8rem;
                margin-bottom: 2rem;
            }

            .stats-bar {
                margin: 0 1rem 40px;
                padding: 1.5rem 1rem;
            }

            .stat-value {
                font-size: 1.5rem;
            }

            .stat-label {
                font-size: 0.75rem;
            }

            .campaign-name {
                font-size: 1.2rem;
            }

            .campaign-price {
                font-size: 2.2rem;
            }

            .campaign-info-grid {
                grid-template-columns: 1fr;
            }

            .feature-card {
                padding: 2rem 1.5rem;
            }

            .feature-icon {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }

            .feature-title {
                font-size: 1.1rem;
            }

            .step-number {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }

            .step-title {
                font-size: 1.1rem;
            }

            .promo-title {
                font-size: 2rem;
            }

            .promo-subtitle {
                font-size: 1.1rem;
            }

            .promo-banner {
                padding: 40px 0;
                margin: 40px 0;
            }

            .features-section,
            .how-it-works {
                padding: 40px 0;
            }

            .features-section::before,
            .features-section::after {
                display: none;
            }

            .navbar-brand {
                font-size: 1.3rem;
            }

            .footer-custom {
                padding: 40px 0 20px;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.8rem;
            }

            .hero-subtitle {
                font-size: 0.9rem;
            }

            .lottery-visual {
                height: 200px;
            }

            .lottery-ball-machine {
                width: 220px;
                height: 220px;
            }

            .cage-inner {
                border-width: 6px;
            }

            .cage-center {
                width: 60px;
                height: 60px;
                border-width: 3px;
            }

            .cage-center span {
                font-size: 0.6rem;
            }

            .wheel-pointer {
                top: -12px;
                border-left: 14px solid transparent;
                border-right: 14px solid transparent;
                border-bottom: 24px solid #ffd32a;
            }

            .wheel-pointer::after {
                width: 18px;
                height: 7px;
                top: 19px;
                left: -9px;
            }

            .prize-label {
                padding-right: 15px;
            }

            .prize-label span {
                font-size: 0.6rem;
                padding: 3px 5px;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .stat-value {
                font-size: 1.3rem;
            }

            .campaign-price {
                font-size: 2rem;
            }

            .btn-primary-custom,
            .btn-secondary-custom {
                font-size: 0.95rem;
                padding: 0.9rem 1.5rem;
            }

            .promo-title {
                font-size: 1.5rem;
            }

            .promo-subtitle {
                font-size: 1rem;
            }
        }

        /* Navbar toggler fix for dark theme */
        .navbar-toggler {
            border-color: rgba(255,255,255,0.3);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
    </style>
</head>
<body>
    <div class="animated-bg"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= url('public') ?>">
                <i class="fas fa-ticket"></i>
                eTickets Raffle
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('public') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('public/howToPlay') ?>">How to Play</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('public/winners') ?>">Winners</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary-custom" href="<?= url('public/buyTicket') ?>" style="padding: 0.5rem 1.5rem; margin-left: 1rem;">
                            <i class="fas fa-ticket"></i> Play Now!
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <div class="hero-badge">
                        <i class="fas fa-fire"></i> LIVE DRAWS DAILY
                    </div>
                    <h1 class="hero-title">
                        Win Big with eTickets Raffle
                    </h1>
                    <p class="hero-subtitle">
                        Join thousands of winners! Play exciting lottery games, win amazing cash prizes, and change your life today.
                    </p>
                    <div class="hero-cta">
                        <a href="<?= url('public/buyTicket') ?>" class="btn btn-primary-custom">
                            <i class="fas fa-ticket"></i> Buy Tickets Now
                        </a>
                        <a href="#campaigns" class="btn btn-secondary-custom">
                            <i class="fas fa-list"></i> View Games
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="lottery-visual">
                        <div class="lottery-machine"></div>
                        <div class="lottery-ball-machine">
                            <div class="wheel-pointer"></div>
                            <div class="lottery-cage">
                                <div class="cage-inner" id="wheelInner">
                                    <!-- Prize labels -->
                                    <div class="prize-label" style="--i:0;"><span>GHS 10</span></div>
                                    <div class="prize-label" style="--i:1;"><span>Try Again</span></div>
                                    <div class="prize-label" style="--i:2;"><span>GHS 50</span></div>
                                    <div class="prize-label" style="--i:3;"><span>Free Ticket</span></div>
                                    <div class="prize-label" style="--i:4;"><span>GHS 20</span></div>
                                    <div class="prize-label" style="--i:5;"><span>Jackpot</span></div>
                                    <div class="prize-label" style="--i:6;"><span>GHS 5</span></div>
                                    <div class="prize-label" style="--i:7;"><span>Bonus Spin</span></div>
                                    
                                    <!-- Center circle -->
                                    <div class="cage-center">
                                        <span>SPIN<br>NOW</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button class="spin-button" id="spinBtn">
                            <i class="fas fa-sync-alt"></i> SPIN THE WHEEL
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <div class="container">
        <div class="stats-bar">
            <div class="row">
                <div class="col-md-4 stat-item">
                    <span class="stat-value">
                        <i class="fas fa-trophy"></i> <?= number_format($stats['total_winners'] ?? 0) ?>+
                    </span>
                    <span class="stat-label">Winners</span>
                </div>
                <div class="col-md-4 stat-item">
                    <span class="stat-value">
                        <i class="fas fa-coins"></i> GHS <?= number_format($stats['total_prizes'] ?? 0, 0) ?>
                    </span>
                    <span class="stat-label">Prizes Awarded</span>
                </div>
                <div class="col-md-4 stat-item">
                    <span class="stat-value">
                        <i class="fas fa-fire"></i> <?= count($campaigns ?? []) ?>
                    </span>
                    <span class="stat-label">Active Games</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Campaigns -->
    <section class="container mb-5" id="campaigns" style="position: relative;">
        <div style="position: absolute; top: -50px; right: 10%; width: 100px; height: 100px; font-size: 5rem; opacity: 0.1; animation: float 3s ease-in-out infinite;">🎲</div>
        <div style="position: absolute; bottom: 20%; left: 5%; width: 100px; height: 100px; font-size: 5rem; opacity: 0.1; animation: float 3s ease-in-out infinite; animation-delay: 1s;">🎯</div>
        
        <h2 class="section-title">
            <i class="fas fa-gamepad"></i> Active Lottery Games
        </h2>
        
        <div class="row">
            <?php if (!empty($campaigns)): ?>
                <?php foreach ($campaigns as $campaign): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="campaign-card">
                            <?php if ($campaign->daily_draw_enabled): ?>
                                <div class="campaign-badge">
                                    <i class="fas fa-bolt"></i> DAILY DRAWS
                                </div>
                            <?php endif; ?>
                            
                            <div class="campaign-header">
                                <h3 class="campaign-name"><?= htmlspecialchars($campaign->name) ?></h3>
                                <div class="campaign-price">
                                    <?= $campaign->currency ?> <?= number_format($campaign->ticket_price, 2) ?>
                                </div>
                                <small>per ticket</small>
                            </div>
                            
                            <div class="campaign-body">
                                <p style="color: rgba(255,255,255,0.8); margin-bottom: 1.5rem;">
                                    <?= htmlspecialchars(substr($campaign->description ?? 'Play and win amazing prizes!', 0, 100)) ?>...
                                </p>
                                
                                <div class="campaign-info-grid">
                                    <div class="info-box">
                                        <div class="info-label">Prize Pool</div>
                                        <div class="info-value"><?= $campaign->prize_pool_percent ?>%</div>
                                    </div>
                                    <div class="info-box">
                                        <div class="info-label">Ends</div>
                                        <div class="info-value"><?= formatDate($campaign->end_date, 'M d') ?></div>
                                    </div>
                                </div>
                                
                                <a href="<?= url('public/buyTicket?campaign=' . $campaign->id) ?>" class="btn btn-campaign">
                                    <i class="fas fa-ticket-alt"></i> Buy Tickets
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No active games at the moment. Check back soon!
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Promo Banner -->
    <div class="promo-banner">
        <div class="container promo-content">
            <h2 class="promo-title">🎉 Win Up To GHS 10,000!</h2>
            <p class="promo-subtitle">Daily draws with instant winners. Your luck starts here!</p>
            <a href="<?= url('public/buyTicket') ?>" class="btn btn-primary-custom btn-lg">
                <i class="fas fa-rocket"></i> Start Playing Now
            </a>
        </div>
    </div>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Why Choose eTickets?</h2>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <h3 class="feature-title">100% Secure</h3>
                        <p class="feature-desc">Bank-level security with encrypted transactions. Your money and data are safe with us.</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="feature-title">Instant Tickets</h3>
                        <p class="feature-desc">Get your ticket codes immediately via SMS. No waiting, start playing right away!</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h3 class="feature-title">Daily Draws</h3>
                        <p class="feature-desc">Multiple chances to win every day. More draws mean more opportunities for you!</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="feature-title">Play via USSD</h3>
                        <p class="feature-desc">No internet? No problem! Dial our USSD code and play from any phone.</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3 class="feature-title">Instant Notifications</h3>
                        <p class="feature-desc">Winners are notified immediately via SMS. Never miss your winning moment!</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <h3 class="feature-title">Quick Payouts</h3>
                        <p class="feature-desc">Get your winnings fast! We process payouts quickly and securely.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h3 class="step-title">Choose Your Game</h3>
                        <p class="step-desc">Browse our exciting lottery games and pick your favorite</p>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h3 class="step-title">Buy Tickets</h3>
                        <p class="step-desc">Purchase tickets easily via mobile money or online payment</p>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h3 class="step-title">Wait for Draw</h3>
                        <p class="step-desc">Daily and final draws are conducted transparently</p>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h3 class="step-title">Win Big!</h3>
                        <p class="step-desc">Winners receive instant SMS notification and quick payouts</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title">
                        <i class="fas fa-ticket"></i> eTickets Raffle
                    </h5>
                    <p style="color: rgba(255,255,255,0.7);">
                        Ghana's most trusted lottery platform. Play responsibly and win big with eTickets!
                    </p>
                    <div style="font-size: 1.5rem; margin-top: 1rem;">
                        <a href="#" style="color: rgba(255,255,255,0.7); margin-right: 1rem;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: rgba(255,255,255,0.7); margin-right: 1rem;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: rgba(255,255,255,0.7); margin-right: 1rem;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="color: rgba(255,255,255,0.7);"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <a href="<?= url('public') ?>" class="footer-link">Home</a>
                    <a href="<?= url('public/howToPlay') ?>" class="footer-link">How to Play</a>
                    <a href="<?= url('public/winners') ?>" class="footer-link">Winners</a>
                    <a href="<?= url('public/buyTicket') ?>" class="footer-link">Buy Tickets</a>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title">Contact Us</h5>
                    <p style="color: rgba(255,255,255,0.7);">
                        <i class="fas fa-phone"></i> +233 XX XXX XXXX<br>
                        <i class="fas fa-envelope"></i> support@etickets.com<br>
                        <i class="fas fa-map-marker-alt"></i> Accra, Ghana
                    </p>
                </div>
            </div>
            
            <hr style="border-color: rgba(255,255,255,0.1); margin: 2rem 0;">
            
            <div class="text-center" style="color: rgba(255,255,255,0.5);">
                <p>&copy; <?= date('Y') ?> eTickets Raffle. All rights reserved. | Play Responsibly | 18+</p>
            </div>
        </div>
    </footer>

    <!-- Result Modal -->
    <div class="result-modal" id="resultModal">
        <div class="result-content">
            <div class="result-icon" id="resultIcon">🎉</div>
            <div class="result-title" id="resultTitle">Congratulations!</div>
            <div class="result-prize" id="resultPrize">GHS 50</div>
            <div class="result-message" id="resultMessage">
                You've won an amazing prize! Ready to win even more?
            </div>
            <div class="result-buttons">
                <a href="<?= url('public/buyTicket') ?>" class="result-btn result-btn-primary">
                    <i class="fas fa-ticket"></i> Buy Tickets Now
                </a>
                <button class="result-btn result-btn-secondary" onclick="closeResultModal()">
                    <i class="fas fa-sync-alt"></i> Spin Again
                </button>
            </div>
        </div>
    </div>

    <script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
    <script src="<?= vendor('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
    // Smooth scroll
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if(target.length) {
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });

    // Add counting animation to stats
    $('.stat-value').each(function() {
        var $this = $(this);
        var text = $this.text();
        var match = text.match(/[\d,]+/);
        if (match) {
            var countTo = parseInt(match[0].replace(/,/g, ''));
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.html($this.html().replace(/[\d,]+/, Math.floor(this.countNum).toLocaleString()));
                },
                complete: function() {
                    $this.html($this.html().replace(/[\d,]+/, this.countNum.toLocaleString()));
                }
            });
        }
    });

    // Wheel Spin Functionality
    const wheelInner = document.getElementById("wheelInner");
    const spinBtn = document.getElementById("spinBtn");

    const prizes = [
        "GHS 10",
        "Try Again",
        "GHS 50",
        "Free Ticket",
        "GHS 20",
        "Jackpot",
        "GHS 5",
        "Bonus"
    ];

    const numSegments = prizes.length;
    const segmentAngle = 360 / numSegments;

    let rotation = 0;
    let isSpinning = false;
    const spinDuration = 4000;

    spinBtn.addEventListener("click", () => {
        if (isSpinning) return;

        isSpinning = true;
        spinBtn.disabled = true;

        // Add several full spins + random extra angle
        const extra = Math.floor(Math.random() * 360);
        const spins = 5;
        const spinAmount = spins * 360 + extra;

        rotation += spinAmount;
        wheelInner.style.transform = `rotate(${rotation}deg)`;

        setTimeout(() => {
            // Normalize rotation to [0, 360)
            const normalizedRotation = ((rotation % 360) + 360) % 360;

            // Pointer is at the top (which is 270 degrees in CSS angle space)
            const pointerAngle = 270;

            // Angle in the wheel that lands under the pointer
            let prizeAngle = pointerAngle - normalizedRotation;
            prizeAngle = ((prizeAngle % 360) + 360) % 360;

            // Determine prize index
            const prizeIndex = Math.floor(prizeAngle / segmentAngle);
            const prize = prizes[prizeIndex];

            // Show result with animation
            setTimeout(() => {
                showResultModal(prize);
                isSpinning = false;
                spinBtn.disabled = false;
            }, 500);
        }, spinDuration);
    });

    // Show result modal with prize-specific content
    function showResultModal(prize) {
        const modal = document.getElementById('resultModal');
        const icon = document.getElementById('resultIcon');
        const title = document.getElementById('resultTitle');
        const prizeEl = document.getElementById('resultPrize');
        const message = document.getElementById('resultMessage');

        // Set prize
        prizeEl.textContent = prize;

        // Customize based on prize
        if (prize === 'Jackpot') {
            icon.textContent = '💰';
            title.textContent = 'JACKPOT!!!';
            message.textContent = 'You hit the JACKPOT! This is your lucky day! Buy tickets now to claim your prize!';
        } else if (prize.includes('GHS')) {
            icon.textContent = '🎉';
            title.textContent = 'Winner!';
            message.textContent = 'Congratulations! You won cash! Buy a ticket now to claim your prize and win even more!';
        } else if (prize === 'Free Ticket') {
            icon.textContent = '🎫';
            title.textContent = 'Free Ticket!';
            message.textContent = 'You won a FREE ticket! Claim it now and double your chances of winning big!';
        } else if (prize === 'Bonus Spin') {
            icon.textContent = '🔄';
            title.textContent = 'Bonus Spin!';
            message.textContent = 'You earned a bonus spin! Buy tickets to unlock more spins and bigger prizes!';
        } else {
            icon.textContent = '😊';
            title.textContent = 'Try Again!';
            message.textContent = 'Almost there! Buy a ticket now and you could win the jackpot on your next spin!';
        }

        modal.classList.add('show');
    }

    // Close result modal
    function closeResultModal() {
        const modal = document.getElementById('resultModal');
        modal.classList.remove('show');
    }

    // Close modal when clicking outside
    document.getElementById('resultModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeResultModal();
        }
    });
    </script>
</body>
</html>
