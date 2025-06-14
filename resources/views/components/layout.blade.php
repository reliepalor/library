<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Library</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        
        .header-container {
            display: flex;
            justify-content: center;
            padding: 1rem 0;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        
        header {
            width: 90%;
            max-width: 1200px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 2rem;
            background: white;
            border-radius: 9999px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        header:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .logo-dot {
            width: 12px;
            height: 12px;
            padding: 12px;
            background-color: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            letter-spacing: -0.025em;
        }
        
        nav {
            display: flex;
            gap: 1.5rem;
        }
        
        nav a {
            text-decoration: none;
            color: #475569;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            position: relative;
        }
        
        nav a:hover {
            color: #0ea5e9;
            background-color: #f0f9ff;
        }
        
        nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #0ea5e9;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        nav a:hover::after {
            width: 70%;
        }
        
        .main-container {
            display: flex;
            justify-content: center;
            padding: 2rem 1rem;
            min-height: calc(100vh - 80px);
        }
        
        .content-wrapper {
            width: 100%;
            max-width: 1400px;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 2rem;
            margin: 1rem;
            border: 1px solid #e2e8f0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .content-wrapper:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }
        
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
                border-radius: 1rem;
            }
            
            nav {
                width: 100%;
                justify-content: center;
            }
            
            .logo-text {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="header-container">
        <header>
            <div class="logo-container">
                <span class="logo-dot"></span>
                <h3 class="logo-text">Library</h3>
            </div>
            <nav>
                <a href="/">Home</a>
                <a href="{{route('admin.books.index')}}">View Books</a>
            </nav>
        </header>
    </div>
    
    <main class="main-container">
        <div class="content-wrapper">
            {{$slot}}
        </div>
    </main>
</body>
</html>