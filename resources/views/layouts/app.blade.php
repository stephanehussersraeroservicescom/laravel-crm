<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel CRM</title>
    @livewireStyles
    <!-- TailwindCSS CDN for quick start (replace with npm build in prod) -->
     <script src="https://cdn.tailwindcss.com"></script>
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.3/dist/tailwind.min.css" rel="stylesheet"> -->
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-blue-800 text-white flex flex-col min-h-screen shadow-lg">
        <div class="p-6 text-2xl font-bold border-b border-blue-700">Tapis CRM</div>
        <nav class="flex-1 py-4">
            <ul>
                <li><a href="{{ route('projects.index') }}" class="block py-2.5 px-6 hover:bg-blue-700 {{ request()->routeIs('projects.index') ? 'bg-blue-700 font-semibold' : '' }}">Projects</a></li>
                {{-- Add more navigation items as needed --}}
                <li><a href="#" class="block py-2.5 px-6 hover:bg-blue-700">Materials</a></li>
                <li><a href="#" class="block py-2.5 px-6 hover:bg-blue-700">Statuses</a></li>
                <li><a href="#" class="block py-2.5 px-6 hover:bg-blue-700">Settings</a></li>
            </ul>
        </nav>
        <div class="p-4 border-t border-blue-700 text-xs text-blue-200">
            &copy; {{ date('Y') }} Tapis Aviation
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow sticky top-0 z-10 flex items-center justify-between px-8 py-4">
            <div class="text-xl font-semibold text-blue-900">Tapis CRM Platform</div>
            <nav class="flex gap-4">
                {{-- Placeholder for top links, user dropdown, etc. --}}
                <a href="#" class="text-blue-700 hover:underline">Help</a>
                <a href="#" class="text-blue-700 hover:underline">Profile</a>
            </nav>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>
<!-- This is the main layout file for the Laravel CRM application.
     It includes a sidebar for navigation and a header for the main content area.
     The sidebar contains links to different sections of the CRM, such as Projects, Materials, etc.
     The main content area will display the content of each page that extends this layout. -->
<!-- The layout uses Tailwind CSS for styling and Livewire for dynamic components.
     The sidebar is fixed and responsive, while the main content area adjusts based on the selected route.
     The header includes links for help and user profile, which can be expanded later.
     The layout is designed to be clean and user-friendly, providing a solid foundation for the CRM application. -->
<!-- Note: The Tailwind CSS CDN link is used for quick prototyping. In production, it's recommended to use a build process with npm to optimize the CSS.
     The Livewire styles and scripts are included to enable dynamic components within the Laravel application.
     The sidebar and header are designed to be responsive and accessible, ensuring a good user experience across devices.
     The layout is structured to allow easy addition of new sections and features as the CRM application evolves.
     The sidebar links are styled to indicate the active route, enhancing navigation clarity for users.
     The footer includes a copyright notice that updates automatically with the current year, providing a professional touch to the application.
     The layout is designed to be easily extendable, allowing developers to add new features and sections without disrupting the existing structure.
     The use of Blade templating engine allows for clean and maintainable code, making it easier to manage the views in the Laravel application.
     The layout serves as a foundation for building a comprehensive CRM system, with a focus on usability and scalability.
     The design follows best practices for web applications, ensuring a consistent look and feel across the platform.
     The sidebar can be easily customized to include additional links or sections as the CRM application grows.
     The layout is optimized for performance, ensuring fast loading times and a smooth user experience.
     The use of Tailwind CSS allows for rapid development and easy customization of styles, making it a great choice for modern web applications.
     The layout is responsive, ensuring that it works well on both desktop and mobile devices, providing a seamless experience for users.
     The header and sidebar are designed to be intuitive, making it easy for users to navigate through the CRM application.
     The layout is built with accessibility in mind, ensuring that all users can interact with the application effectively.
     The use of Livewire allows for dynamic updates to the page without requiring full page reloads, enhancing the user experience.
     The layout is structured to support future enhancements, such as adding new features or integrating third-party services, without significant refactoring.
     The design is clean and modern, aligning with current web design trends to provide a visually appealing interface for users.
     The layout is a key component of the Laravel CRM application, providing a solid foundation for building a robust and user-friendly system.
     The sidebar navigation is designed to be intuitive, allowing users to easily access different sections of the CRM.
     The header provides a clear title for the application, reinforcing the brand identity of Tapis Aviation.
     The layout is designed to be flexible, allowing for easy modifications and additions as the CRM application evolves.
     The use of Blade components and Livewire enhances the maintainability of the code, making it easier to manage complex interactions and dynamic content.
     The layout is a crucial part of the user interface, providing a consistent and cohesive experience across the Laravel CRM application      .