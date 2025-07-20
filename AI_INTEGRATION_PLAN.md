# AI Integration Plan for Laravel CRM

## Overview
Date: 2025-07-18
Purpose: Implement a unified AI service for multiple features across the application

## Requirements
1. **Single AI service** with monthly usage-based billing
2. **Multiple use cases:**
   - Web browsing/scraping (SeatGuru seat configurations)
   - Automatic report generation
   - Data extraction and analysis
   - General automation tasks

## Current Implementations Needing AI
1. **Seat Configuration Auto-Population**
   - Location: `app/Console/Commands/PopulateAircraftSeats.php`
   - Current: Simulated data only
   - Need: Real SeatGuru data extraction
   - Triggered by: Project creation (via `app/Observers/ProjectObserver.php`)

2. **Future: Report Generation**
   - Automatic reporting functionality (to be implemented)
   - Will need AI for content generation and analysis

## Recommended AI Services Architecture

### Primary Options
1. **OpenAI GPT-4 API**
   - Pros: Function calling, structured outputs, wide capability
   - Use for: General tasks, report generation
   - Pricing: Pay per token

2. **Perplexity API**
   - Pros: Excellent web search, real-time data
   - Use for: SeatGuru lookups, web data extraction
   - Pricing: Monthly usage-based

3. **Anthropic Claude API**
   - Pros: Long context, strong analysis
   - Use for: Complex report generation
   - Pricing: Pay per token

### Recommended Implementation Strategy
1. Create AI service configuration in `.env`
2. Build abstraction layer: `app/Services/AiService.php`
3. Support multiple providers with fallback
4. Implement caching for repeated queries
5. Add usage tracking for cost management

## Next Steps
1. Choose primary AI service(s)
2. Set up API keys and billing
3. Create service integration layer
4. Update seat configuration to use real AI
5. Implement report generation feature

## Code Locations to Update
- `app/Console/Commands/PopulateAircraftSeats.php` - Replace simulated data
- `app/Observers/ProjectObserver.php` - Already triggers AI lookup
- `config/services.php` - Add AI service configurations
- Create: `app/Services/AiService.php` - Main AI integration layer
- Create: `app/Services/WebScraperService.php` - For SeatGuru specific logic

## Configuration Example
```php
// .env
AI_SERVICE=openai
OPENAI_API_KEY=your-key-here
PERPLEXITY_API_KEY=your-key-here

// config/services.php
'ai' => [
    'default' => env('AI_SERVICE', 'openai'),
    'services' => [
        'openai' => [
            'key' => env('OPENAI_API_KEY'),
            'model' => 'gpt-4-turbo-preview',
        ],
        'perplexity' => [
            'key' => env('PERPLEXITY_API_KEY'),
        ],
    ],
],
```

## Notes
- User mentioned having a previous discussion about tokens and AI services
- Wants unified solution to avoid multiple subscriptions
- Cost-effectiveness is important (monthly usage-based)
- Multiple features will use the same AI service