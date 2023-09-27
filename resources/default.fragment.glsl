#version 330 core
out vec4 fragment_color;
in vec4 fColor;

void main()
{
    fragment_color = fColor;
}